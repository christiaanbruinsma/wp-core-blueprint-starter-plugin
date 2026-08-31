# Architecture

## Purpose

The starter is a conformance specimen for the Core Blueprint public extension boundary. A normal extension should be buildable from this structure plus Base's public documentation without reaching into Base internals.

## Ownership model

```text
Core Blueprint Base
├── public lifecycle contracts
├── ExtensionRegistry
├── PageRegistry / Core Admin shell
├── Design Foundation primitives
├── health/status normalization
└── Governance storage/policy
        ↓
Core Blueprint extension
├── product identity
├── feature runtime
├── product-specific composition/layout
└── declarations of the Base contracts it consumes
```

Base is authoritative. The extension never creates a compatibility implementation of Base-owned behavior.

## Bootstrap

`core-blueprint-starter.php` owns only bootstrap-safe concerns:

- plugin constants;
- the extension autoloader;
- translation loading on `init`;
- Core API compatibility checking;
- activation dependency enforcement;
- inert runtime behavior when Base is unavailable;
- late handoff to `CB\\Starter\\Plugin` after active plugins have loaded.

The bootstrap does not load a standalone admin interface.

## Plugin wiring

`src/Plugin.php` wires the small set of example integrations. It contains no business logic.

The default starter demonstrates:

- suite identity and health;
- one Core Admin page;
- one extension-owned CSS file;
- one Governance event definition and an unused example write method.

Remove examples that a real extension does not need.

## Extension identity

`CB\\Core\\ExtensionRegistry` is the canonical platform identity boundary. The starter uses one ID:

`core-blueprint-starter-plugin`

`plugin_file` is only the WordPress inventory locator. `status_id` references the separate health registry and is not a second identity.

Compatibility is expressed with Core API `major.minor`. Exact Base release checks should only be introduced when a concrete product release is genuinely required beyond the public API contract.

## Core Admin page

`CB\\Starter\\Admin\\Page` implements `CB\\Core\\Admin\\Page` directly. It does not inherit Base's internal `PageBase` convenience class.

Registration happens on `cb_core_register_pages` and declares only the shared components used by the markup:

- `panels`
- `notices`

The page automatically receives the minimal Core Admin shell from Base. Private asset handles and bundle filenames are never referenced.

## Design Foundation

The extension owns composition, not shared appearance.

The starter's CSS therefore contains only a product-owned grid wrapper and consumes Base spacing tokens. It contains no selectors that redraw `.cb-core-*` primitives.

When a future Base release changes panel radius, surface colour, typography, focus treatment or light/dark theme values without changing the semantic contract, the starter should update visually without an extension release.

A structural markup or interaction contract change is different and may legitimately require an extension update.

## Status

The health provider is registered through `cb_core_module_status_definitions`. Base invokes providers lazily when status is requested and validates the `ok|warn|err|off` shape.

The example provider is intentionally cheap and read-only. Real status providers must not hide mutations, repair work or expensive request-hot operations.

## Governance

Event metadata is registered through `CB\\Core\\Governance\\EventRegistry` on `init` or later. Event writes go only through `CB\\Core\\Governance\\Audit::record()`.

Do not use:

- `cb_core_event_labels`;
- `CB\\Core\\Log\\AuditLog`;
- Base repositories or storage classes.

The starter does not emit its example event automatically.

## Persistent data and uninstall

The starter intentionally persists no product data of its own. It therefore does not ship an `uninstall.php` placeholder or a destructive uninstall routine.

When deriving a real extension, add uninstall behavior only for data the extension itself owns. Do not remove Base-owned state, shared registry data, audit storage or unrelated WordPress content. If a feature uses custom tables, options, scheduled events or generated files, document its ownership and explicit uninstall policy before adding cleanup code.

A no-op uninstall file is not useful boilerplate: absence of owned persistent data is the canonical default for this starter.

## Optional subsystems

Database schema registration, module activation, REST, AJAX, cron and other feature-specific concerns are deliberately excluded from active boilerplate.

Add them only when the extension needs them and only against documented Base/WordPress contracts. See `RECIPES.md` for the small set of currently verified examples.

## Public versus internal

The governing rule is simple:

> Only contracts documented in Base's `docs/PUBLIC-API.md` and linked Foundation documents are stable extension API.

Class visibility alone is not a compatibility promise.
