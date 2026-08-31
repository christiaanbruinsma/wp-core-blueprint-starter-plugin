# Starter tooling

The starter intentionally keeps tooling small and local. It does not define a separate Core Blueprint release pipeline.

## `conformance.php`

Run from the plugin repository root:

```bash
php tools/conformance.php
```

The command exits with status `0` on success and `1` when the starter violates one of its frozen source-level boundaries.

Current checks include:

- no private Base `cb-core-css-*` handles;
- no legacy `cb_core_event_labels` mutation;
- no direct `CB\\Core\\Log\\AuditLog` writes;
- no private `AdminAssetCatalog` access;
- no direct WordPress menu registration for Core Admin pages;
- no `PageBase` dependency;
- no `Requires Plugins` header for first-party runtime dependency enforcement;
- no request-time `filemtime()` asset versioning;
- no jQuery dependency;
- no local redraw of canonical Base appearance on `.cb-core-*` primitives.

The conformance script is deliberately a source invariant checker, not a substitute for PHP syntax checks, WordPress runtime tests or staging verification.

## Expected development checks

Before treating a derived extension as release-ready, run at minimum:

1. `php tools/conformance.php`;
2. PHP syntax/static checks for all runtime PHP files;
3. activation with a compatible Core Blueprint Base;
4. activation without Base, confirming clean refusal;
5. runtime with Base later unavailable, confirming the extension becomes inert;
6. Core Admin screen checks in both light and dark themes;
7. browser/PHP logs for missing asset dependencies, warnings and early translation notices.

## Failure handling

Do not weaken a conformance rule merely to make a derived plugin pass. First determine whether the plugin is violating the current Base public contract.

If Base intentionally changes a public contract, update the starter only after the new Base contract is documented and frozen. The Base `docs/PUBLIC-API.md` and linked Foundation documentation remain authoritative.

## Packaging

This directory does not contain a release builder yet. Core Blueprint Base is still finalizing the suite-wide reproducible packaging workflow. Once that workflow is frozen, the starter should consume the same canonical process rather than maintaining a plugin-specific builder.

The immutable packaging rule already applies: a release ZIP must contain the canonical installed plugin folder as its single plugin root.

## Maintenance

Keep this tooling dependency-free where practical. It should remain readable, fast and safe to run locally without booting WordPress or mutating repository contents.
