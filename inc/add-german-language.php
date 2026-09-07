<?php
/**
 * Temporary one-shot: register German (de) with Polylang.
 *
 * Polylang ships no REST route for adding a language, and the MCP ability
 * registry on this install only serves Rank Math's catalog. Hand-inserting the
 * `language` term over the REST term endpoint would skip everything
 * PLL_Admin_Model::add_language() does around that insert — the paired
 * `term_language` term, the serialized locale/flag description, the languages
 * cache, and the rewrite-rule flush that makes /de/ resolve at all. So the
 * language is added through Polylang's own admin model instead, once, from a
 * theme file that runs on any request.
 *
 * The result is written to a `degerman`-prefixed option because that is the
 * only shape the MCP connector can read back (wp_get_plugin_settings looks
 * options up by prefix; wp_get_option serves an allowlist this is not on).
 *
 * Delete this file, its require in functions.php, and the option it wrote
 * (bump NORDICTV_ADD_DE_VERSION to 0 to have it purged) once /de/ resolves.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump to re-run. Set to 0 to purge the option it wrote.
define( 'NORDICTV_ADD_DE_VERSION', 1 );

/**
 * The language to add, in the shape PLL_Admin_Model::add_language() expects.
 *
 * term_group orders the language switcher; 10 puts German after the six that
 * are already there without renumbering any of them.
 *
 * @return array<string,mixed>
 */
function nordictv_add_de_args() {
	return array(
		'name'       => 'Deutsch',
		'slug'       => 'de',
		'locale'     => 'de_DE',
		'rtl'        => 0,
		'term_group' => 10,
		'flag'       => 'de',
	);
}

/**
 * Add German once per version bump, on any request.
 */
function nordictv_add_de_maybe_run() {
	$state = get_option( 'degerman_state' );

	if ( is_array( $state ) && (int) ( $state['version'] ?? -1 ) === (int) NORDICTV_ADD_DE_VERSION ) {
		return;
	}

	// Version 0 means: clean up after yourself.
	if ( 0 === (int) NORDICTV_ADD_DE_VERSION ) {
		delete_option( 'degerman_state' );
		return;
	}

	$result = array(
		'version'   => (int) NORDICTV_ADD_DE_VERSION,
		'generated' => wp_date( 'Y-m-d H:i:s' ),
	);

	if ( ! function_exists( 'PLL' ) || ! class_exists( 'PLL_Admin_Model' ) ) {
		$result['status'] = 'polylang-missing';
		update_option( 'degerman_state', $result, false );
		return;
	}

	// Already there? Record that and stop — add_language() would only error.
	if ( term_exists( 'de', 'language' ) ) {
		$result['status'] = 'already-exists';
		update_option( 'degerman_state', $result, false );
		return;
	}

	// PLL() hands back a PLL_Model on front-end and REST requests; only the
	// admin subclass carries add_language(), so instantiate it directly.
	$options = PLL()->options;
	$model   = new PLL_Admin_Model( $options );

	$added = $model->add_language( nordictv_add_de_args() );

	if ( is_wp_error( $added ) ) {
		$result['status'] = 'error';
		$result['errors'] = $added->get_error_messages();
	} else {
		$result['status'] = 'added';
		$result['term']   = term_exists( 'de', 'language' );
	}

	update_option( 'degerman_state', $result, false );
}

// Priority 99 so Polylang has finished booting on `init` before this runs.
add_action( 'init', 'nordictv_add_de_maybe_run', 99 );
