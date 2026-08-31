# Optional Integration Recipes

The starter keeps optional systems out of the active runtime. Add a recipe only when the product actually needs it, and re-check Base's current `docs/PUBLIC-API.md` before implementation.

## Add another Design Foundation requirement

Declare the semantic capability on the page instead of adding a raw asset dependency:

```php
PageRegistry::register(
    new Page(),
    [
        'foundations' => [ 'modal' ],
        'components'  => [ 'panels', 'notices', 'state-badges' ],
    ]
);
```

Only declare primitives the page actually renders.

Do not depend on private handles such as `cb-core-css-panels` or on Base's internal asset catalog.

## Record a Governance event

Register event metadata at `init` or later:

```php
EventRegistry::register( [
    'id'                 => 'vendor.item.updated',
    'label'              => __( 'Vendor item updated', 'vendor-plugin' ),
    'retention_category' => 'maintenance',
] );
```

Record the real mutation through the single public facade:

```php
Audit::record(
    'vendor.item.updated',
    'notice',
    [ 'item_id' => $item_id ]
);
```

Public event IDs are dotted lower-case identifiers. Do not use Base-reserved namespaces, direct AuditLog access or `cb_core_event_labels`.

## Add an optional Base-managed module switch

Only use the module activation contract when the feature genuinely needs a canonical enabled/disabled state controlled by Base.

The state class implements:

```php
use CB\Core\Modules\ModuleStateInterface;

final class State implements ModuleStateInterface {
    public static function is_enabled(): bool {
        return (bool) get_option( 'vendor_feature_enabled', false );
    }

    public static function set_enabled( bool $enabled, string $actor = 'unknown' ): void {
        update_option( 'vendor_feature_enabled', $enabled ? 1 : 0, false );
    }
}
```

Register it declaratively:

```php
add_filter( 'cb_core_module_activation_definitions', static function ( array $definitions ): array {
    $definitions['vendor-feature'] = [
        'state'      => State::class,
        'capability' => 'manage_options',
    ];
    return $definitions;
} );
```

Activation state and health are separate. Do not overload the status provider as the master-switch authority.

## Add extension-owned database tables

Use `CB\\Core\\Database\\SchemaRegistry` only when WordPress-native options/meta/content are not an appropriate storage model.

```php
SchemaRegistry::register( [
    'id'         => 'vendor-jobs',
    'version'    => '1.0',
    'option_key' => 'vendor_jobs_db_version',
    'tables'     => [ [ Vendor\Jobs\Schema::class, 'jobs_table' ] ],
    'install'    => [ Vendor\Jobs\Schema::class, 'install' ],
] );
```

The installer must be idempotent and re-runnable. It does not advance the version marker itself; Base owns reconciliation, verification and marker advancement.

The extension still owns its table schema and uninstall policy.

## Add browser JavaScript

There is no JavaScript in the starter by default.

When a feature needs it:

- use vanilla JavaScript;
- scope the script to the exact screen or runtime context;
- keep localized/runtime data lazy and minimal;
- use documented Base Foundations for shared interactions such as modal, toast, time picker or token input;
- do not copy Foundation behavior into extension JavaScript.

## REST, AJAX, admin-post and cron

These are WordPress runtime mechanisms, not reasons to invent a generic starter abstraction.

When a real feature needs one:

1. choose the narrow WordPress-native lifecycle for that operation;
2. add explicit capability/nonce/permission boundaries;
3. keep management and runtime contexts separate;
4. register only the hooks needed in that context;
5. use documented Base public services only where Base genuinely owns the cross-suite concern.

This document intentionally does not provide a made-up Base wrapper for these mechanisms.
