# Core Blueprint Extension Starter

Minimal production-grade starting point for a **Core Blueprint extension**.

This repository is deliberately not a feature plugin. It demonstrates the smallest useful integration with the public Core Blueprint Base v1 contracts while keeping Base in control of shared Core Admin presentation.

## Reference baseline

The starter was authored against:

- Core Blueprint Base public API: `1.0`
- verified source reference: Base `main` @ `09808d906a963bba6fd4e58ecde4aa870b0db3c8` (`1.0.0-rc1`)
- WordPress: `7.0+`
- PHP: `8.4+`

Runtime compatibility is based on `CB_CORE_API_VERSION`, not on the exact Base product version.

## What this starter demonstrates

- hard Base dependency with fail-safe inert runtime when Base is unavailable;
- canonical `CB\\Core\\ExtensionRegistry` registration;
- a `CB\\Core\\Admin\\Page` registered through `PageRegistry`;
- semantic Core Admin Design Foundation requirements;
- exact page-scoped extension CSS through `PageRegistry::hook_suffix()`;
- lazy module health through `cb_core_module_status_definitions`;
- Governance metadata through `EventRegistry` and writes through `Audit::record()`;
- WordPress-safe translation loading on `init`;
- a deliberately non-loaded Automation Foundation provider reference with `semantic_type` examples;
- no jQuery, compatibility bridge, service container or standalone fallback.

Optional systems such as custom database tables, REST, AJAX, cron and module activation are intentionally **not** active boilerplate. The Automation Foundation example is also reference-only and is not loaded by the Starter. See `docs/RECIPES.md` and `docs/AUTOMATION-PROVIDER.md`.

## Before using this starter

Treat the repository as source material. Do not ship it unchanged.

Perform one complete identity pass before feature development:

1. Rename the canonical plugin folder from `core-blueprint-starter-plugin` to the new plugin slug.
2. Rename `core-blueprint-starter.php` to the new canonical plugin entry filename.
3. Update the plugin header name, description and text domain.
4. Replace the `CB_STARTER_*` constant prefix.
5. Replace the `CB\\Starter\\` namespace and the autoloader prefix.
6. Replace extension ID `core-blueprint-starter-plugin` in `Integration\\Suite`.
7. Replace `Admin\\Page::SLUG` with the new globally unique lower-case kebab-case page slug.
8. Replace `cb-starter-*` asset handles and extension-owned CSS classes.
9. Replace the example Governance namespace/event (`starter.example.updated`) with real product-domain events, or remove it when the plugin has no governance-relevant mutations.
10. If adopting the Automation Foundation reference, replace every `example.*` capability, Starter semantic identity and fail-fast callback before loading it.
11. Re-run `php tools/conformance.php`.

For first-party Core Blueprint extensions, the ExtensionRegistry ID and canonical plugin folder use the `core-blueprint-*` namespace and the plugin Author header remains exactly `Core Blueprint`.

A derived extension should also narrow the bootstrap dependency check to the public Base contracts it actually uses. For example, if the Core Admin page example is removed, do not keep `PageRegistry` and `Page` as artificial runtime prerequisites. Dependency checks document real consumption, not every contract demonstrated by the original Starter.

## Design ownership

Core Blueprint Base owns the canonical appearance of shared Core Admin primitives.

A page declares semantic requirements:

```php
PageRegistry::register(
    new Page(),
    [
        'components' => [ 'panels', 'notices' ],
    ]
);
```

That declaration means:

```text
semantic component ID
→ documented markup/behavior contract
→ Base-owned presentation/assets
```

It does **not** expose CSS handles or filenames.

Extension CSS may compose product-specific layout:

```css
.cb-my-extension-grid {
    display: grid;
    gap: var(--cb-space-4);
}
```

It must not locally redraw generic Base primitives such as panels, notices, cards, badges, buttons, form controls or tabs.

## Automation ownership

Automation Foundation follows a parallel ownership rule:

> **Extensions own business semantics. Base owns interoperability. Automations owns orchestration.**

The Starter's provider reference demonstrates capability declaration only. It does not emit triggers, invoke actions or state resolvers, define workflows, evaluate conditions, schedule work, retry failures or persist run history.

See `docs/AUTOMATION-PROVIDER.md` before adapting `examples/AutomationProvider.php`.

## Base dependency behavior

This starter has no standalone mode.

- Activation is refused when compatible Core Blueprint Base public contracts are unavailable.
- If Base is later deactivated or becomes API-incompatible, the starter stays inert and shows an administrator dependency notice.
- No alternate admin menu or duplicate design layer is created.

This is intentional: Core Blueprint extensions adapt to Base rather than maintaining a second runtime contract.

## Source layout

```text
core-blueprint-starter-plugin/
├── core-blueprint-starter.php
├── src/
│   ├── Plugin.php
│   ├── Integration/
│   │   └── Suite.php
│   ├── Admin/
│   │   ├── Page.php
│   │   └── Assets.php
│   └── Governance/
│       └── Events.php
├── examples/
│   └── AutomationProvider.php
├── assets/
│   └── css/
│       └── admin.css
├── languages/
├── docs/
│   ├── ARCHITECTURE.md
│   ├── AUTOMATION-PROVIDER.md
│   ├── EXTENSION-CHECKLIST.md
│   └── RECIPES.md
└── tools/
    └── conformance.php
```

## Development flow

1. Complete the identity pass.
2. Decide which public Base contracts the feature actually needs.
3. Register only those contracts and remove unused Starter dependency checks/examples.
4. Keep runtime code feature-specific and narrowly scoped.
5. If the extension exposes automation capabilities, keep provider adapters thin and provider-owned; do not add an Automations dependency for discovery.
6. Use Base semantic components instead of private `cb-core-css-*` handles.
7. Run the conformance script.
8. Test activation with and without compatible Base.
9. Test the extension page in both Core Blueprint light and dark themes.
10. Verify no PHP notices, missing WordPress style dependencies or early translation warnings occur.

## Packaging

A canonical release ZIP must contain the canonical plugin folder as its single plugin root. Do not rename the internal plugin root to a version, branch, temporary build directory or GitHub archive name.

This starter intentionally does **not** ship its own release builder yet. A suite-wide executable extension build contract has not been frozen; do not invent a plugin-specific packaging pipeline and mistake it for a Core Blueprint platform guarantee.

## Public API authority

When this starter and Base disagree, Base documentation wins. In particular, consult Base's:

- `docs/PUBLIC-API.md`
- `docs/AUTOMATION-FOUNDATION.md`
- `docs/CORE-ADMIN-DESIGN-FOUNDATION.md`
- Foundation-specific contract documents

A PHP class or method being `public` does not automatically make it a supported Core Blueprint extension API.
