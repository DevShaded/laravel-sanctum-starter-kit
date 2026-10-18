# Design Patterns in Laravel

## Purpose

Use design patterns when they solve real problems.

Do not introduce patterns purely to demonstrate architectural knowledge.

Prefer Laravel's native capabilities whenever appropriate.

## Strategy Pattern

Use when multiple interchangeable behaviors share a common contract.

Examples:

- Payment gateways
- Notification providers
- Pricing algorithms
- Export formats

```php
interface ExportStrategy
{
    public function export(Collection $records): string;
}
```

```php
class CsvExportStrategy implements ExportStrategy
{
    public function export(Collection $records): string
    {
        // Generate CSV
    }
}
```

```php
class JsonExportStrategy implements ExportStrategy
{
    public function export(Collection $records): string
    {
        return $records->toJson();
    }
}
```

Use strategies when variations are substantial or independently evolving.

Simple conditionals are acceptable for small, stable cases.

## Factory Pattern

Use factories when object construction or implementation selection is complex.

Laravel already provides:

- Model factories
- Service container bindings
- Contextual bindings

Do not duplicate these mechanisms unnecessarily.

## Adapter Pattern

Use adapters to isolate external services.

Example:

```php
interface SmsGateway
{
    public function send(
        string $recipient,
        string $message
    ): void;
}
```

A provider-specific adapter implements this contract.

Business logic depends on the application contract rather than vendor-specific APIs.

## Observer Pattern

Laravel provides model observers.

Use them for appropriate model lifecycle behavior.

Avoid hiding important workflows in observers when the operation should be explicit.

Consider transaction and after-commit behavior.

## State Pattern

Use when different states have substantial, distinct behavior.

Examples:

- Booking workflows
- Order workflows
- Subscription lifecycles

For simpler workflows, a PHP enum and explicit transition logic may be sufficient.

Do not create a separate state class for every status unless it adds value.

## Decorator Pattern

Use when behavior needs to be wrapped or enhanced without modifying the original implementation.

Examples:

- Caching
- Metrics
- Logging
- Retry behavior

Prefer Laravel middleware or other native mechanisms when they already solve the problem.

## Dependency Injection

Laravel's service container supports constructor injection and interface bindings.

Use it to manage dependencies cleanly.

Do not mistake dependency injection itself for a requirement to create interfaces.

## Repository Pattern

Use repositories only when they provide a meaningful persistence boundary.

Avoid repositories that simply duplicate Eloquent methods.

## Choosing Patterns

Before introducing a pattern, ask:

1. What problem does it solve?
2. Is the problem present now?
3. Does Laravel already provide a solution?
4. Does the pattern improve readability?
5. Does it reduce meaningful coupling?
6. Is the additional complexity justified?

## Final Rule

Patterns are tools, not requirements.
