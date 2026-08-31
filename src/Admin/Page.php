<?php
declare(strict_types=1);
/**
 * Page - minimal Core Admin example page.
 *
 * @package CB_Starter
 */

namespace CB\Starter\Admin;

use CB\Core\Admin\Page as PageContract;
use CB\Core\Admin\PageRegistry;
use CB\Core\UI\Notice;

defined( 'ABSPATH' ) || exit;

final class Page implements PageContract {
	public const SLUG = 'core-blueprint-starter-plugin';

	public static function init(): void {
		add_action( 'cb_core_register_pages', [ __CLASS__, 'register' ] );
	}

	public static function register(): void {
		PageRegistry::register(
			new self(),
			[
				'components' => [
					'panels',
					'notices',
				],
			]
		);
	}

	public function slug(): string {
		return self::SLUG;
	}

	public function title(): string {
		return __( 'Starter Plugin', 'core-blueprint-starter' );
	}

	public function menu_title(): string {
		return __( 'Starter Plugin', 'core-blueprint-starter' );
	}

	public function capability(): string {
		return 'manage_options';
	}

	public function position(): ?int {
		return null;
	}

	public function render(): void {
		if ( ! current_user_can( $this->capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'core-blueprint-starter' ) );
		}
		?>
		<div class="wrap cb-core-wrap cb-starter-wrap">
			<h1 class="cb-core-title"><?php esc_html_e( 'Core Blueprint Starter Plugin', 'core-blueprint-starter' ); ?></h1>
			<p class="cb-core-intro">
				<?php esc_html_e( 'A minimal extension page that consumes Base-owned design primitives and keeps product-specific layout inside the extension.', 'core-blueprint-starter' ); ?>
			</p>

			<?php
			echo Notice::render( [
				'variant' => Notice::INFO,
				'title'   => __( 'Base owns the shared design', 'core-blueprint-starter' ),
				'message' => __( 'This notice and the panels below are styled by Core Blueprint Base. The starter CSS only composes them into an example layout.', 'core-blueprint-starter' ),
			] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Base renderer returns escaped component HTML.
			?>

			<div class="cb-starter-example-grid">
				<section class="cb-core-panel">
					<h2><?php esc_html_e( 'Public contracts', 'core-blueprint-starter' ); ?></h2>
					<p><?php esc_html_e( 'Extension identity, admin registration, health and governance use documented Base APIs instead of Base internals.', 'core-blueprint-starter' ); ?></p>
				</section>

				<section class="cb-core-panel">
					<h2><?php esc_html_e( 'Extension ownership', 'core-blueprint-starter' ); ?></h2>
					<p><?php esc_html_e( 'Keep feature-specific composition here. Shared surfaces, controls, states and themes remain owned by Base.', 'core-blueprint-starter' ); ?></p>
				</section>
			</div>
		</div>
		<?php
	}
}
