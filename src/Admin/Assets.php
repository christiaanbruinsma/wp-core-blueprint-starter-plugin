<?php
declare(strict_types=1);
/**
 * Assets - extension-owned assets only.
 *
 * Base-owned Design Foundation presentation is requested semantically through
 * PageRegistry in Page::register(); it is never referenced through raw handles.
 *
 * @package CB_Starter
 */

namespace CB\Starter\Admin;

use CB\Core\Admin\PageRegistry;

defined( 'ABSPATH' ) || exit;

final class Assets {
	private const HANDLE = 'cb-starter-admin';

	public static function init(): void {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue' ], 20 );
	}

	public static function enqueue( string $hook ): void {
		$expected = PageRegistry::hook_suffix( Page::SLUG );
		if ( ! is_string( $expected ) || '' === $expected || $hook !== $expected ) {
			return;
		}

		wp_enqueue_style(
			self::HANDLE,
			CB_STARTER_URL . 'assets/css/admin.css',
			[],
			CB_STARTER_VERSION
		);
	}
}
