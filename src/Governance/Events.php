<?php
declare(strict_types=1);
/**
 * Events - canonical Governance example.
 *
 * The example event is registered but never emitted automatically. Replace its
 * namespace and meaning with real product-domain events when creating a plugin
 * from this starter.
 *
 * @package CB_Starter
 */

namespace CB\Starter\Governance;

use CB\Core\Governance\Audit;
use CB\Core\Governance\EventRegistry;

defined( 'ABSPATH' ) || exit;

final class Events {
	public const EXAMPLE_UPDATED = 'starter.example.updated';

	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'register' ], 10 );
	}

	public static function register(): void {
		EventRegistry::register( [
			'id'                 => self::EXAMPLE_UPDATED,
			'label'              => __( 'Starter example updated', 'core-blueprint-starter' ),
			'retention_category' => 'general',
		] );
	}

	/**
	 * Example public Audit facade call for a real domain mutation.
	 *
	 * Nothing in the starter calls this method. It exists as executable reference
	 * code and should be renamed or removed when deriving a real extension.
	 */
	public static function record_example( int $item_id ): bool {
		if ( $item_id <= 0 ) {
			return false;
		}

		return Audit::record(
			self::EXAMPLE_UPDATED,
			'notice',
			[ 'item_id' => $item_id ]
		);
	}
}
