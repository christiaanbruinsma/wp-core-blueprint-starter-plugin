# Automation Provider Reference

The Starter includes `examples/AutomationProvider.php` as a **non-loaded reference** for Core Blueprint Base Automation Foundation providers.

It is intentionally not active boilerplate.

Core Blueprint Base can discover provider-owned triggers, state capabilities and actions without Core Blueprint Automations being installed. The optional Automations product can later orchestrate those contracts.

> **Extensions own business semantics. Base owns interoperability. Automations owns orchestration.**

## Why the example is not loaded

A generic starter does not own a real product domain. It therefore cannot provide a truthful production resolver, action executor, event boundary or idempotency model.

Loading fake behavior would teach the wrong architecture and make the Starter resemble a miniature workflow product.

The reference file consequently lives under `examples/`, outside the Starter autoload/runtime path. Its resolver and executor fail fast until a developer replaces them with real provider-owned domain services.

## What the example demonstrates

`examples/AutomationProvider.php` shows:

- registration through `cb_core_register_automation_capabilities`;
- provider identity matching `Integration\Suite::ID`;
- one Trigger, one read-only State capability and one Action contract;
- stable dotted capability IDs and schema version `1`;
- primitive transport schemas;
- provider-specific semantic identity such as `core-blueprint-starter.record_id`;
- shared semantic identities such as `wp.user_id` and `core-blueprint.source_title`;
- an explicit required WordPress capability for State/Action contracts;
- provider-owned resolver/executor callbacks without exposing orchestration behavior.

It deliberately does **not** demonstrate workflow definitions, conditions, scheduling, queues, retries, run history, action invocation, state invocation or a workflow UI.

## Adapting it to a real extension

Copy the file into the real extension's `src/` tree only after the domain capability is understood.

Then:

1. Replace the namespace and provider identity as part of the normal Starter identity pass.
2. Replace `example.*` capability IDs with stable domain names.
3. Replace `core-blueprint-starter.record_id` with a provider-owned semantic identity.
4. Keep shared semantic IDs only when the meaning is intentionally interoperable.
5. Replace `resolve_current()` with a read-only adapter over the canonical domain service/repository.
6. Replace `archive_record()` with a thin adapter over the canonical mutation/service API.
7. Set the real `required_capability` for each callable capability.
8. Add the provider's `init()` call to the plugin bootstrap only after those implementations are real.
9. Add regression coverage for capability IDs, schema versions, semantic identities and the absence of accidental orchestration logic.

Do not copy the example and leave the fail-fast callbacks in an enabled provider.

## Semantic types

`semantic_type` adds domain meaning without changing the primitive runtime value.

```php
'record_id' => [
    'type'          => 'integer',
    'semantic_type' => 'my-extension.record_id',
    'required'      => true,
],
```

The value is still an integer. The semantic identity helps an orchestration consumer distinguish that record ID from another integer such as a WordPress user ID.

Examples of deliberate shared semantics include:

```text
wp.user_id
core-blueprint.completion_date
core-blueprint.source_title
```

Provider-owned concepts should normally use the provider's own namespace.

## Discovery boundary

Public consumers can discover capability metadata through Base registries. State resolvers and Action executors are intentionally withheld from public discovery.

Discovery is not execution authority.

Do not use reflection or internal Base classes to invoke callbacks. Do not add a direct dependency on Core Blueprint Automations just to expose provider capabilities.

## Trigger emission is a separate domain decision

Base also has a trigger-delivery API, but this Starter reference intentionally does not emit anything.

A real extension should wire emission only at its canonical domain event boundary and only after deciding on stable event/idempotency identity. That belongs in the real product implementation, not in generic Starter boilerplate.

## Product boundary

The Starter is a public reference implementation for extension developers. It is not a free edition of Core Blueprint Automations.

It must not grow a workflow builder, workflow persistence, scheduler, condition engine, queue/worker system, retries, execution history or other orchestration runtime behavior.

## Normative source

When adapting this reference, verify it against current Core Blueprint Base:

- `docs/PUBLIC-API.md`
- `docs/AUTOMATION-FOUNDATION.md`
- `CB\Core\Automation\TriggerRegistry`
- `CB\Core\Automation\StateRegistry`
- `CB\Core\Automation\ActionRegistry`
- `CB\Core\Automation\Schema`

Base public contracts remain authoritative.
