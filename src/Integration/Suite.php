<?php
declare(strict_types=1);
/**
 * Suite - canonical Core Blueprint extension and health registration.
 *
 * @package CB_Starter
 */

namespace CB\Starter\Integration;

use CB\Core\ExtensionRegistry;
use CB\Starter\Admin\Page;

defined( 'ABSPATH' ) || exit;

final class Suite {
	public const ID = 'core-blueprint-starter-plugin';

	public static function init(): void {
		add_action( 'cb_core_register_extensions', [ __CLASS__, 'register_extension' ] );
		add_filter( 'cb_core_module_status_definitions', [ __CLASS__, 'register_status_definition' ] );
	}

	public static function register_extension(): void {
		ExtensionRegistry::register( [
			'id'           => self::ID,
			'plugin_file'  => CB_STARTER_BASENAME,
			'requires_api' => CB_STARTER_REQUIRED_API,
			'menu_url'     => admin_url( 'admin.php?page=' . Page::SLUG ),
			'status_id'    => self::ID,
		] );
	}

	/**
	 * @param array<string,array<string,mixed>> $definitions Existing status definitions.
	 * @return array<string,array<string,mixed>>
	 */
	public static function register_status_definition( array $definitions ): array {
		$definitions[ self::ID ] = [
			'provider' => [ __CLASS__, 'status' ],
			'label'    => 'Starter Plugin',
			'url'      => admin_url( 'admin.php?page=' . Page::SLUG ),
		];

		return $definitions;
	}

	/** @return array{state:string,detail:string,url:string} */
	public static function status(): array {
		return [
			'state'  => 'ok',
			'detail' => __( 'Connected through the Core Blueprint public extension contracts.', 'core-blueprint-starter' ),
			'url'    => admin_url( 'admin.php?page=' . Page::SLUG ),
		];
	}
}
