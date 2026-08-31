# Core Blueprint Extension Checklist

Use this checklist before turning the starter into a real plugin.

## 1. Identity

- [ ] Canonical plugin folder selected and fixed.
- [ ] Main plugin filename selected and fixed.
- [ ] Plugin name and description updated.
- [ ] Text domain updated.
- [ ] PHP namespace updated from `CB\\Starter`.
- [ ] Constant prefix updated from `CB_STARTER_`.
- [ ] ExtensionRegistry ID updated.
- [ ] Admin page slug updated.
- [ ] Extension asset handles/classes updated.
- [ ] Governance event namespace updated or removed.
- [ ] First-party plugin Author header remains exactly `Core Blueprint`.

## 2. Base dependency

- [ ] The plugin is intentionally a Core Blueprint extension.
- [ ] Activation fails cleanly without compatible Base.
- [ ] Runtime becomes inert if Base is later unavailable.
- [ ] No standalone Core Admin fallback is introduced.
- [ ] Compatibility is based on `CB_CORE_API_VERSION` unless an exact Base release is genuinely required.

## 3. Core Admin

If the extension needs a page beneath Core Blueprint:

- [ ] Page implements `CB\\Core\\Admin\\Page` directly.
- [ ] Page registers on `cb_core_register_pages`.
- [ ] Position is `null` or `>= 100`.
- [ ] Capability is explicit and appropriate for the feature.
- [ ] Only actually used semantic `foundations`/`components` are declared.
- [ ] Extension assets use `PageRegistry::hook_suffix()` for exact screen scoping.
- [ ] No raw `cb-core-css-*` dependency is used.
- [ ] No Base-internal asset catalog is referenced.

If the extension does not need a Core Admin page, remove the example `Admin` classes and CSS rather than leaving dormant boilerplate.

## 4. Design ownership

- [ ] Shared Base primitives are consumed using their canonical markup/component contracts.
- [ ] Extension CSS owns feature-specific composition only.
- [ ] Extension CSS does not redraw Base panels, cards, tabs, badges, notices, buttons, form controls or states.
- [ ] Extension-specific components use `--cb-*` design tokens where practical.
- [ ] Light and dark Core Blueprint themes are both tested.
- [ ] Responsive behavior is tested where the extension owns layout.

## 5. Runtime scope

- [ ] Disabled/unused functionality has no request-hot side effects.
- [ ] Expensive work is not performed merely to render status or navigation.
- [ ] No hidden repair/migration is performed in read-only providers.
- [ ] Hooks are registered only in the contexts that need them.
- [ ] No jQuery is introduced; browser behavior uses vanilla JavaScript when JavaScript is needed.

## 6. Status and Dashboard integration

If status is useful:

- [ ] Status definition uses `cb_core_module_status_definitions`.
- [ ] Provider is lazy and read-only.
- [ ] Provider returns only `ok|warn|err|off`.
- [ ] Detail is short and factual.
- [ ] Provider failures do not need custom exception plumbing; Base owns normalization/fail-safe presentation.

If status is not useful, remove `status_id` and the example status definition.

## 7. Governance

For governance-relevant mutations:

- [ ] Event IDs use dotted lower-case identifiers.
- [ ] Event namespace belongs to the extension and is not Base-reserved.
- [ ] Human-readable metadata registers through `EventRegistry` on `init` or later.
- [ ] Writes use only `Governance\\Audit::record()`.
- [ ] Context contains identifiers/facts, not secrets or sensitive payload bodies.
- [ ] One of the five canonical retention categories is used when an explicit category is needed.
- [ ] No direct `AuditLog`, `cb_core_event_labels` or Base storage access exists.

If the plugin has no governance-relevant mutations, remove the example `Governance\\Events` class.

## 8. Persistent data

Before adding persistent storage, answer explicitly:

- Does a WordPress option/meta/taxonomy already model this correctly?
- Does the extension truly need a custom table?
- Who owns creation, verification and uninstall?
- Is the installer idempotent and re-runnable?

Do not add custom schema machinery pre-emptively.

## 9. Security

- [ ] Every mutation has an explicit capability check.
- [ ] Browser mutations use an appropriate nonce.
- [ ] Object-level actions use object-level capabilities where applicable.
- [ ] Input is validated/sanitized at the boundary.
- [ ] Output is escaped for its context.
- [ ] Filesystem/network operations fail closed where partial execution would be unsafe.
- [ ] No secret is written to logs, localized script data or HTML.

## 10. Internationalization

- [ ] Source strings use the plugin text domain.
- [ ] Text domain loads on `init` or later.
- [ ] No translation is resolved during early plugin-file inclusion.
- [ ] EN is the source language.
- [ ] Release translation plan covers NL/DE/FR/ES/IT/PT where required by the suite release policy.

## 11. Packaging and release

- [ ] Internal ZIP root folder equals the canonical installed plugin folder.
- [ ] No version/build/temporary name replaces the canonical root folder.
- [ ] Development-only files are excluded according to the suite-wide release process.
- [ ] `php tools/conformance.php` passes.
- [ ] PHP syntax/static checks pass.
- [ ] Activation/deactivation smoke tests pass.
- [ ] Runtime tested with compatible Base and with Base dependency unavailable.
- [ ] No PHP notices, missing style dependencies or early-translation warnings are present.

Do not invent a plugin-specific release pipeline while Core Blueprint Base still owns/finalizes the suite-wide packaging contract.
