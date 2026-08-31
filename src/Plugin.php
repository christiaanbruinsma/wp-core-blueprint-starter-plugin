<?php
declare(strict_types=1);
/**
 * Plugin - minimal runtime wiring only.
 *
 * @package CB_Starter
 */

namespace CB\Starter;

use CB\Starter\Admin\Assets;
use CB\Starter\Admin\Page;
use CB\Starter\Governance\Events;
use CB\Starter\Integration\Suite;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		Suite::init();
		Events::init();

		if ( is_admin() ) {
			Page::init();
			Assets::init();
		}
	}
}
