<?php
declare(strict_types=1);
/**
 * AutomationProvider - non-loaded Automation Foundation reference.
 *
 * This file is intentionally outside src/ and is not loaded by the Starter.
 * Copy it into a real extension only after replacing the example domain IDs,
 * resolver and executor with canonical provider-owned implementations.
 *
 * @package CB_Starter
 */

namespace CB\Starter\Examples;

use CB\Core\Automation\ActionRegistry;
use CB\Core\Automation\StateRegistry;
use CB\Core\Automation\TriggerRegistry;
use CB\Starter\Integration\Suite;

defined( 'ABSPATH' ) || exit;

final class AutomationProvider {
	public static function init(): void {
		add_action( 'cb_core_register_automation_capabilities', [ self::class, 'register' ] );
	}

	public static function register(): void {
		if (
			! class_exists( TriggerRegistry::class )
			|| ! class_exists( StateRegistry::class )
			|| ! class_exists( ActionRegistry::class )
		) {
			return;
		}

		TriggerRegistry::register(
			[
				'provider'       => Suite::ID,
				'id'             => 'example.changed',
				'label'          => __( 'Example changed', 'core-blueprint-starter' ),
				'description'    => __( 'Reference trigger for a provider-owned domain event.', 'core-blueprint-starter' ),
				'schema_version' => '1',
				'payload_schema' => [
					'record_id' => [
						'type'          => 'integer',
						'semantic_type' => 'core-blueprint-starter.record_id',
						'required'      => true,
					],
					'user_id' => [
						'type'          => 'integer',
						'semantic_type' => 'wp.user_id',
						'required'      => true,
					],
				],
			]
		);

		StateRegistry::register(
			[
				'provider'            => Suite::ID,
				'id'                  => 'example.current',
				'label'               => __( 'Current example state', 'core-blueprint-starter' ),
				'description'         => __( 'Reference read-only state capability.', 'core-blueprint-starter' ),
				'schema_version'      => '1',
				'input_schema'        => [
					'record_id' => [
						'type'          => 'integer',
						'semantic_type' => 'core-blueprint-starter.record_id',
						'required'      => true,
					],
				],
				'output_schema'       => [
					'record_id' => [
						'type'          => 'integer',
						'semantic_type' => 'core-blueprint-starter.record_id',
						'required'      => true,
					],
					'title' => [
						'type'          => 'string',
						'semantic_type' => 'core-blueprint.source_title',
						'required'      => true,
					],
					'status' => [
						'type'     => 'string',
						'required' => true,
					],
				],
				'required_capability' => 'manage_options',
				'resolver'            => [ self::class, 'resolve_current' ],
			]
		);

		ActionRegistry::register(
			[
				'provider'            => Suite::ID,
				'id'                  => 'example.archive',
				'label'               => __( 'Archive example record', 'core-blueprint-starter' ),
				'description'         => __( 'Reference provider-owned action capability.', 'core-blueprint-starter' ),
				'schema_version'      => '1',
				'input_schema'        => [
					'record_id' => [
						'type'          => 'integer',
						'semantic_type' => 'core-blueprint-starter.record_id',
						'required'      => true,
					],
				],
				'output_schema'       => [
					'archived' => [
						'type'     => 'boolean',
						'required' => true,
					],
				],
				'required_capability' => 'manage_options',
				'executor'            => [ self::class, 'archive_record' ],
			]
		);
	}

	/**
	 * Replace with a read-only call to the extension's canonical domain service.
	 *
	 * @param array<string,mixed> $input
	 * @return array<string,mixed>
	 */
	public static function resolve_current( array $input ): array {
		unset( $input );
		throw new \LogicException( 'Starter reference only: implement the provider-owned state resolver before enabling this example.' );
	}

	/**
	 * Replace with a call to the extension's canonical mutation/service layer.
	 *
	 * @param array<string,mixed> $input
	 * @return array<string,mixed>
	 */
	public static function archive_record( array $input ): array {
		unset( $input );
		throw new \LogicException( 'Starter reference only: implement the provider-owned action executor before enabling this example.' );
	}
}
