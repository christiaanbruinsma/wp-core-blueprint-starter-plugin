<?php
/**
 * Plugin Name:       Core Blueprint Starter Plugin
 * Plugin URI:        https://coreblueprint.io
 * Description:       Minimal production-grade starter for Core Blueprint extensions.
 * Version:           0.1.0
 * Author:            Core Blueprint
 * Author URI:        https://coreblueprint.io
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       core-blueprint-starter
 * Domain Path:       /languages
 * Requires at least: 7.0
 * Requires PHP:      8.4
 *
 * @package CB_Starter
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

define( 'CB_STARTER_VERSION',      '0.1.0' );
define( 'CB_STARTER_REQUIRED_API', '1.0' );
define( 'CB_STARTER_FILE',         __FILE__ );
define( 'CB_STARTER_DIR',          plugin_dir_path( __FILE__ ) );
define( 'CB_STARTER_URL',          plugin_dir_url( __FILE__ ) );
define( 'CB_STARTER_BASENAME',     plugin_basename( __FILE__ ) );

/**
 * Minimal PSR-4 style autoloader for this extension.
 *
 * Copying the starter requires changing the namespace prefix as part of the
 * identity pass documented in README.md.
 */
spl_autoload_register( static function ( string $class ): void {
	$prefix = 'CB\\Starter\\';
	$length = strlen( $prefix );

	if ( 0 !== strncmp( $class, $prefix, $length ) ) {
		return;
	}

	$relative = substr( $class, $length );
	$file     = CB_STARTER_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';

	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

/** Load this plugin's translations at the WordPress-safe lifecycle point. */
add_action( 'init', static function (): void {
	load_plugin_textdomain(
		'core-blueprint-starter',
		false,
		dirname( CB_STARTER_BASENAME ) . '/languages'
	);
}, 1 );

/** Whether the active Base Core API satisfies this extension's major/minor requirement. */
function cb_starter_api_compatible( string $available, string $required ): bool {
	if ( 1 !== preg_match( '/^(\d+)\.(\d+)$/', $available, $available_match ) ) {
		return false;
	}
	if ( 1 !== preg_match( '/^(\d+)\.(\d+)$/', $required, $required_match ) ) {
		return false;
	}

	return (int) $available_match[1] === (int) $required_match[1]
		&& (int) $available_match[2] >= (int) $required_match[2];
}

/**
 * Check only the documented Base contracts this starter needs.
 *
 * No fallback UI is installed when the dependency is unavailable. The extension
 * remains inert until a compatible Base is active again.
 */
function cb_starter_base_ready(): bool {
	if ( ! defined( 'CB_CORE_API_VERSION' ) ) {
		return false;
	}
	if ( ! cb_starter_api_compatible( (string) CB_CORE_API_VERSION, CB_STARTER_REQUIRED_API ) ) {
		return false;
	}

	return class_exists( '\\CB\\Core\\ExtensionRegistry' )
		&& class_exists( '\\CB\\Core\\Admin\\PageRegistry' )
		&& interface_exists( '\\CB\\Core\\Admin\\Page' );
}

/** Human-readable dependency state for the runtime admin notice. */
function cb_starter_dependency_message(): string {
	if ( ! defined( 'CB_CORE_API_VERSION' ) ) {
		return __( 'Core Blueprint Starter Plugin requires an active Core Blueprint Base plugin.', 'core-blueprint-starter' );
	}

	if ( ! cb_starter_api_compatible( (string) CB_CORE_API_VERSION, CB_STARTER_REQUIRED_API ) ) {
		return sprintf(
			/* translators: 1: required Core API version, 2: available Core API version. */
			__( 'Core Blueprint Starter Plugin requires Core API %1$s or a newer compatible minor version. This site provides %2$s.', 'core-blueprint-starter' ),
			CB_STARTER_REQUIRED_API,
			(string) CB_CORE_API_VERSION
		);
	}

	return __( 'Core Blueprint Starter Plugin cannot access the required public Base contracts.', 'core-blueprint-starter' );
}

/** Hard dependency gate for interactive plugin activation. */
function cb_starter_activate(): void {
	if ( cb_starter_base_ready() ) {
		return;
	}

	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	deactivate_plugins( CB_STARTER_BASENAME );

	wp_die(
		esc_html( 'Core Blueprint Starter Plugin requires an active, Core API 1.x compatible Core Blueprint Base installation.' ),
		esc_html( 'Core Blueprint dependency required' ),
		[ 'back_link' => true ]
	);
}
register_activation_hook( __FILE__, 'cb_starter_activate' );

/**
 * Boot after all active plugins have loaded.
 *
 * Base owns the platform. If Base disappears or becomes incompatible after this
 * extension was activated, the extension stays inert rather than creating a
 * parallel standalone admin experience.
 */
add_action( 'plugins_loaded', static function (): void {
	if ( ! cb_starter_base_ready() ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', static function (): void {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				printf(
					'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Core Blueprint Starter Plugin:', 'core-blueprint-starter' ),
					esc_html( cb_starter_dependency_message() )
				);
			} );
		}
		return;
	}

	\CB\Starter\Plugin::boot();
}, 30 );
