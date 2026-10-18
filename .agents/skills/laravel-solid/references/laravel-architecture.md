# Laravel Architecture Guidelines

## Purpose

Design maintainable Laravel applications using framework conventions and clear responsibilities.

Prefer Laravel-native architecture before introducing custom patterns.

## Core Principles

1. Respect the existing project structure.
2. Follow the installed Laravel and PHP versions.
3. Use Laravel's built-in capabilities.
4. Keep responsibilities cohesive.
5. Avoid unnecessary architectural layers.
6. Choose architecture based on actual application complexity.

## Recommended Structure

```text
app/
├── Actions/
├── Contracts/
├── Enums/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   ├── Resources/
│   └── Middleware/
├── Jobs/
├── Listeners/
├── Models/
├── Notifications/
├── Policies/
├── Providers/
└── Services/
```

Not every directory is required.

Do not introduce directories without a meaningful need.

## Controllers

Controllers should:

- Handle HTTP requests.
- Delegate complex business operations.
- Return appropriate responses.
- Remain focused on transport concerns.

### Example

```php
class BookingController extends Controller
{
    public function store(
        StoreBookingRequest $request,
        CreateBookingAction $action
    ): BookingResource {
        $booking = $action->execute(
            $request->validated()
        );

        return new BookingResource($booking);
    }
}
```

For simple CRUD operations, using Eloquent directly inside controllers is acceptable.

Do not require a service or action for every controller method.

## Form Requests

Use Form Requests for reusable or substantial validation and request authorization.

```php
class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'provider_id' => ['required', 'exists:users,id'],
            'starts_at' => ['required', 'date', 'after:now'],
        ];
    }
}
```

Form Request validation does not replace business-rule enforcement.

Availability conflicts and similar domain invariants should be handled within the application operation.

## Actions

Use Actions for focused business operations.

Examples:

- CreateBookingAction
- CancelBookingAction
- ConfirmBookingAction
- RescheduleBookingAction

Actions should have a clearly defined responsibility.

Do not create Actions that merely forward a single trivial call without adding value.

## Services

Use Services for related business capabilities or workflows.

Examples:

- AvailabilityService
- BookingConflictService
- PricingService

Avoid large services that contain unrelated operations.

Avoid introducing Actions and Services that only delegate to each other.

## Models

Eloquent models should define:

- Relationships
- Casts
- Scopes
- Relevant entity behavior
- Suitable mass-assignment configuration

Models do not have to be entirely passive data containers.

Keep relevant domain behavior close to the entity when appropriate.

## Policies

Use Policies for resource-level authorization.

Examples:

- Can a customer view this booking?
- Can a provider confirm this booking?
- Can an administrator modify this service?

Prefer explicit authorization to scattered role checks.

## API Resources

Use API Resources to transform models into API responses.

- Keep output consistent.
- Avoid exposing sensitive fields.
- Avoid introducing unnecessary database queries.
- Load required relationships appropriately.

## Events and Listeners

Use events to represent meaningful occurrences.

Examples:

- BookingCreated
- BookingConfirmed
- BookingCancelled

Use listeners for independent reactions such as sending notifications.

Do not use events merely to hide important business logic.

Consider transaction timing and after-commit execution.

## Jobs

Use Jobs for:

- Background processing
- Slow integrations
- Large imports
- Expensive reports
- Asynchronous notifications

Design jobs with retries, failures, and duplicate execution in mind.

## Service Container

Use constructor dependency injection for explicit dependencies.

Bind interfaces to implementations when meaningful.

Avoid repeatedly calling the container directly from business logic.

## Architecture Selection

### Simple CRUD

Use controllers, Form Requests, Policies, Eloquent, and Resources as needed.

### Complex Business Logic

Consider a focused Action or Service.

### Multiple External Integrations

Consider contracts and adapters.

### Independent Side Effects

Consider Events, Listeners, and Jobs.

### Complex Domain

Consider richer domain models and clearer module boundaries.

## Final Rule

Do not force Clean Architecture, DDD, CQRS, or repository layers onto a Laravel project without a concrete reason.
