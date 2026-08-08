<?php
/**
 * Temporary read-only reader for the WP Activity Log (Melapress) tables.
 *
 * WP Activity Log keeps its events in its own tables and ships no REST route,
 * so this registers a WordPress ability that reads them over the authenticated
 * MCP channel. Nothing is exposed publicly. Remove once the audit is done.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Columns present on the occurrences table, cached per request.
 *
 * @return array<string, true>
 */
function nordictv_wsal_occurrence_columns() {
	static $columns = null;

	if ( null !== $columns ) {
		return $columns;
	}

	global $wpdb;

	$columns = array();
	$table   = $wpdb->base_prefix . 'wsal_occurrences';
	$rows    = $wpdb->get_col( "SHOW COLUMNS FROM `{$table}`" ); // phpcs:ignore WordPress.DB

	foreach ( (array) $rows as $row ) {
		$columns[ $row ] = true;
	}

	return $columns;
}

/**
 * Metadata rows for a set of occurrence ids, keyed by occurrence id.
 *
 * @param int[] $ids Occurrence ids.
 * @return array<int, array<string, string>>
 */
function nordictv_wsal_metadata_for( array $ids ) {
	global $wpdb;

	if ( empty( $ids ) ) {
		return array();
	}

	$table       = $wpdb->base_prefix . 'wsal_metadata';
	$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
	$rows         = $wpdb->get_results(
		$wpdb->prepare( "SELECT occurrence_id, name, value FROM `{$table}` WHERE occurrence_id IN ({$placeholders})", $ids ) // phpcs:ignore WordPress.DB
	);

	$meta = array();

	foreach ( (array) $rows as $row ) {
		$value = maybe_unserialize( $row->value );

		if ( is_array( $value ) || is_object( $value ) ) {
			$value = wp_json_encode( $value );
		}

		$meta[ (int) $row->occurrence_id ][ $row->name ] = (string) $value;
	}

	return $meta;
}

add_action(
	'abilities_api_init',
	function () {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		wp_register_ability(
			'nordictv/read-activity-log',
			array(
				'label'               => 'Read WP Activity Log events',
				'description'         => 'Reads events from the WP Activity Log tables. Filter by username to see everything one account did. Set list_users to true to get the distinct usernames present in the log with their event counts and first/last seen dates.',
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'username'   => array(
							'type'        => 'string',
							'description' => 'Exact username to filter on. Omit to return every event.',
						),
						'list_users' => array(
							'type'        => 'boolean',
							'description' => 'Return the distinct usernames in the log instead of events.',
						),
						'limit'      => array(
							'type'        => 'integer',
							'description' => 'Maximum events to return (default 200, max 1000).',
						),
						'offset'     => array(
							'type'        => 'integer',
							'description' => 'Events to skip, for paging through a large result set.',
						),
					),
				),
				'output_schema'       => array(
					'type' => 'object',
				),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'execute_callback'    => function ( $input = array() ) {
					global $wpdb;

					$table = $wpdb->base_prefix . 'wsal_occurrences';

					if ( $table !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) ) {
						return array( 'error' => 'wsal_occurrences table not found — WP Activity Log may store events elsewhere on this install.' );
					}

					$columns  = nordictv_wsal_occurrence_columns();
					$has_user = isset( $columns['username'] );
					$meta_tbl = $wpdb->base_prefix . 'wsal_metadata';

					// Usernames live on the occurrences row in WPAL 4.x+ and in the
					// metadata table on older schemas.
					if ( ! empty( $input['list_users'] ) ) {
						if ( $has_user ) {
							$rows = $wpdb->get_results( "SELECT username, COUNT(*) AS events, MIN(created_on) AS first_seen, MAX(created_on) AS last_seen FROM `{$table}` GROUP BY username ORDER BY events DESC", ARRAY_A ); // phpcs:ignore WordPress.DB
						} else {
							$rows = $wpdb->get_results( "SELECT value AS username, COUNT(*) AS events, NULL AS first_seen, NULL AS last_seen FROM `{$meta_tbl}` WHERE name = 'Username' GROUP BY value ORDER BY events DESC", ARRAY_A ); // phpcs:ignore WordPress.DB
						}

						foreach ( $rows as &$row ) {
							foreach ( array( 'first_seen', 'last_seen' ) as $key ) {
								if ( ! empty( $row[ $key ] ) ) {
									$row[ $key ] = wp_date( 'Y-m-d H:i:s', (int) $row[ $key ] );
								}
							}
						}

						return array(
							'schema' => $has_user ? 'occurrences.username' : 'metadata.Username',
							'users'  => $rows,
						);
					}

					$limit  = min( 1000, max( 1, (int) ( $input['limit'] ?? 200 ) ) );
					$offset = max( 0, (int) ( $input['offset'] ?? 0 ) );
					$user   = isset( $input['username'] ) ? trim( (string) $input['username'] ) : '';

					$select = "SELECT o.* FROM `{$table}` o";
					$where  = '';
					$args   = array();

					if ( '' !== $user ) {
						if ( $has_user ) {
							$where  = ' WHERE o.username = %s';
							$args[] = $user;
						} else {
							$select = "SELECT o.* FROM `{$table}` o INNER JOIN `{$meta_tbl}` m ON m.occurrence_id = o.id";
							$where  = ' WHERE m.name = %s AND m.value = %s';
							$args[] = 'Username';
							$args[] = $user;
						}
					}

					$sql    = $select . $where . ' ORDER BY o.created_on DESC LIMIT %d OFFSET %d';
					$args[] = $limit;
					$args[] = $offset;

					$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A ); // phpcs:ignore WordPress.DB
					$ids  = wp_list_pluck( (array) $rows, 'id' );
					$meta = nordictv_wsal_metadata_for( array_map( 'intval', $ids ) );

					$events = array();

					foreach ( (array) $rows as $row ) {
						$id = (int) $row['id'];

						$events[] = array(
							'id'         => $id,
							'date'       => wp_date( 'Y-m-d H:i:s', (int) $row['created_on'] ),
							'event_id'   => isset( $row['alert_id'] ) ? (int) $row['alert_id'] : null,
							'severity'   => $row['severity'] ?? null,
							'object'     => $row['object'] ?? null,
							'event_type' => $row['event_type'] ?? null,
							'username'   => $row['username'] ?? ( $meta[ $id ]['Username'] ?? null ),
							'user_roles' => $row['user_roles'] ?? null,
							'client_ip'  => $row['client_ip'] ?? null,
							'post_id'    => isset( $row['post_id'] ) ? (int) $row['post_id'] : null,
							'post_type'  => $row['post_type'] ?? null,
							'meta'       => $meta[ $id ] ?? array(),
						);
					}

					return array(
						'filtered_username' => '' !== $user ? $user : null,
						'returned'          => count( $events ),
						'limit'             => $limit,
						'offset'            => $offset,
						'events'            => $events,
					);
				},
			)
		);
	}
);
