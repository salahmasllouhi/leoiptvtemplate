<?php
/**
 * Temporary read-only export of the WP Activity Log (Melapress) tables.
 *
 * WP Activity Log keeps its events in its own tables, ships no REST route, and
 * the MCP ability registry on this install only serves Rank Math's own catalog.
 * So the dump is written into prefixed, non-autoloaded options that the MCP
 * connector can read back. Nothing is exposed publicly.
 *
 * Delete this file, its require in functions.php, and the options it wrote
 * (bump NORDICTV_WSAL_DUMP_VERSION to 0 to have them purged) once the audit is
 * done.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump to re-run the dump. Set to 0 to purge every option it wrote.
define( 'NORDICTV_WSAL_DUMP_VERSION', 1 );

// Usernames matching this LIKE pattern get their full event history exported.
define( 'NORDICTV_WSAL_DUMP_MATCH', '%rankmath%' );

const NORDICTV_WSAL_DUMP_CHUNK = 25;
const NORDICTV_WSAL_DUMP_MAX   = 600;

/**
 * Option names written by the dump, so cleanup can find them again.
 *
 * @return string[]
 */
function nordictv_wsal_dump_option_names() {
	$names = array( 'auditdump_state', 'auditdump_users' );

	for ( $i = 1; $i <= (int) ceil( NORDICTV_WSAL_DUMP_MAX / NORDICTV_WSAL_DUMP_CHUNK ); $i++ ) {
		$names[] = sprintf( 'advlogchunk%02d_events', $i );
	}

	return $names;
}

/**
 * Run the export once per version bump, on any request.
 */
function nordictv_wsal_dump_maybe_run() {
	global $wpdb;

	$state = get_option( 'auditdump_state' );

	if ( is_array( $state ) && (int) ( $state['version'] ?? -1 ) === (int) NORDICTV_WSAL_DUMP_VERSION ) {
		return;
	}

	// Version 0 means: clean up after yourself.
	if ( 0 === (int) NORDICTV_WSAL_DUMP_VERSION ) {
		foreach ( nordictv_wsal_dump_option_names() as $name ) {
			delete_option( $name );
		}

		update_option( 'auditdump_state', array( 'version' => 0, 'purged' => true ), false );
		return;
	}

	$occurrences = $wpdb->base_prefix . 'wsal_occurrences';
	$metadata    = $wpdb->base_prefix . 'wsal_metadata';

	if ( $occurrences !== $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $occurrences ) ) ) {
		update_option(
			'auditdump_state',
			array(
				'version' => (int) NORDICTV_WSAL_DUMP_VERSION,
				'error'   => 'wsal_occurrences table not found',
				'tables'  => $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', '%wsal%' ) ),
			),
			false
		);
		return;
	}

	$columns = array();

	foreach ( (array) $wpdb->get_col( "SHOW COLUMNS FROM `{$occurrences}`" ) as $column ) { // phpcs:ignore WordPress.DB
		$columns[ $column ] = true;
	}

	$has_username = isset( $columns['username'] );

	// Who appears in the log at all — lets the audit confirm the exact spelling
	// of the account name before reading the events.
	if ( $has_username ) {
		$users = $wpdb->get_results( "SELECT username, user_id, COUNT(*) AS events, MIN(created_on) AS first_seen, MAX(created_on) AS last_seen FROM `{$occurrences}` GROUP BY username, user_id ORDER BY events DESC LIMIT 100", ARRAY_A ); // phpcs:ignore WordPress.DB
	} else {
		$users = $wpdb->get_results( "SELECT value AS username, NULL AS user_id, COUNT(*) AS events, NULL AS first_seen, NULL AS last_seen FROM `{$metadata}` WHERE name = 'Username' GROUP BY value ORDER BY events DESC LIMIT 100", ARRAY_A ); // phpcs:ignore WordPress.DB
	}

	foreach ( $users as &$user ) {
		foreach ( array( 'first_seen', 'last_seen' ) as $key ) {
			if ( ! empty( $user[ $key ] ) ) {
				$user[ $key ] = wp_date( 'Y-m-d H:i:s', (int) $user[ $key ] );
			}
		}
	}

	unset( $user );

	if ( $has_username ) {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT o.* FROM `{$occurrences}` o WHERE o.username LIKE %s ORDER BY o.created_on ASC LIMIT %d", // phpcs:ignore WordPress.DB
				NORDICTV_WSAL_DUMP_MATCH,
				NORDICTV_WSAL_DUMP_MAX
			),
			ARRAY_A
		);
	} else {
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT o.* FROM `{$occurrences}` o INNER JOIN `{$metadata}` m ON m.occurrence_id = o.id WHERE m.name = 'Username' AND m.value LIKE %s ORDER BY o.created_on ASC LIMIT %d", // phpcs:ignore WordPress.DB
				NORDICTV_WSAL_DUMP_MATCH,
				NORDICTV_WSAL_DUMP_MAX
			),
			ARRAY_A
		);
	}

	$meta = array();
	$ids  = array_map( 'intval', wp_list_pluck( (array) $rows, 'id' ) );

	if ( $ids ) {
		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		$meta_rows    = $wpdb->get_results(
			$wpdb->prepare( "SELECT occurrence_id, name, value FROM `{$metadata}` WHERE occurrence_id IN ({$placeholders})", $ids ) // phpcs:ignore WordPress.DB
		);

		foreach ( (array) $meta_rows as $meta_row ) {
			$value = maybe_unserialize( $meta_row->value );

			if ( is_array( $value ) || is_object( $value ) ) {
				$value = wp_json_encode( $value );
			}

			$meta[ (int) $meta_row->occurrence_id ][ $meta_row->name ] = (string) $value;
		}
	}

	$events = array();

	foreach ( (array) $rows as $row ) {
		$id = (int) $row['id'];

		$events[] = array_filter(
			array(
				'date'       => wp_date( 'Y-m-d H:i:s', (int) $row['created_on'] ),
				'event_id'   => isset( $row['alert_id'] ) ? (int) $row['alert_id'] : null,
				'object'     => $row['object'] ?? null,
				'event_type' => $row['event_type'] ?? null,
				'username'   => $row['username'] ?? ( $meta[ $id ]['Username'] ?? null ),
				'roles'      => $row['user_roles'] ?? null,
				'ip'         => $row['client_ip'] ?? null,
				'post_id'    => ! empty( $row['post_id'] ) ? (int) $row['post_id'] : null,
				'post_type'  => $row['post_type'] ?? null,
				'meta'       => $meta[ $id ] ?? array(),
			),
			static function ( $value ) {
				return null !== $value && '' !== $value && array() !== $value;
			}
		);
	}

	$chunks = array_chunk( $events, NORDICTV_WSAL_DUMP_CHUNK );

	foreach ( nordictv_wsal_dump_option_names() as $name ) {
		if ( 0 === strpos( $name, 'advlogchunk' ) ) {
			delete_option( $name );
		}
	}

	foreach ( $chunks as $index => $chunk ) {
		update_option( sprintf( 'advlogchunk%02d_events', $index + 1 ), $chunk, false );
	}

	update_option( 'auditdump_users', $users, false );
	update_option(
		'auditdump_state',
		array(
			'version'      => (int) NORDICTV_WSAL_DUMP_VERSION,
			'match'        => NORDICTV_WSAL_DUMP_MATCH,
			'schema'       => $has_username ? 'occurrences.username' : 'metadata.Username',
			'total_events' => count( $events ),
			'chunks'       => count( $chunks ),
			'chunk_size'   => NORDICTV_WSAL_DUMP_CHUNK,
			'capped'       => count( $events ) >= NORDICTV_WSAL_DUMP_MAX,
			'generated'    => wp_date( 'Y-m-d H:i:s' ),
		),
		false
	);
}

add_action( 'init', 'nordictv_wsal_dump_maybe_run', 99 );
