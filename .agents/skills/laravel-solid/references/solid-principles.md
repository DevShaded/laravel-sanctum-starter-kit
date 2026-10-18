# SOLID Principles for Laravel

## Purpose

Apply SOLID principles to improve Laravel code quality, maintainability, and extensibility without introducing unnecessary complexity.

SOLID principles are guidelines, not rigid rules.

Always prefer the simplest implementation that meets the application's actual requirements.

## 1. Single Responsibility Principle (SRP)

A class should have one primary responsibility and one reason to change.

### Laravel Guidelines

- Controllers handle HTTP requests and responses.
- Form Requests handle validation and request authorization.
- Policies handle resource authorization.
- Actions encapsulate focused business operations.
- Services coordinate related workflows.
- Eloquent models represent entities and relevant domain behavior.
- API Resources transform application data for responses.
- Jobs handle background work.
- Listeners respond to events.

### Bad Example

```php
class BookingController extends Controller
{
    public function store(Request $request)
    {
        $booking = Booking::create($request->all());

        Mail::to($booking->customer->email)
            ->send(new BookingCreatedMail($booking));

        return response()->json($booking);
    }
}
```

The controller handles persistence, notification delivery, and HTTP concerns. Request data is also used without an explicit validation boundary.

### Better Example

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

```php
class CreateBookingAction
{
    public function execute(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $booking = Booking::create($data);

            BookingCreated::dispatch($booking);

            return $booking;
        });
    }
}
```

This illustrates responsibility separation, not a complete booking implementation. A real booking action must enforce availability, authorization boundaries, and concurrency rules. Event listeners relying on committed records must be configured for after-commit execution.

### Rules

- Keep classes cohesive.
- Extract complex business workflows when justified.
- Avoid mixing unrelated infrastructure concerns.
- Do not create classes solely to reduce method length.
- Simple CRUD operations may remain in controllers.

## 2. Open/Closed Principle (OCP)

Components should support extension without requiring unnecessary changes to existing, stable business logic.

### Example

```php
interface NotificationGateway
{
    public function send(string $recipient, string $message): void;
}
```

```php
class SmsNotificationGateway implements NotificationGateway
{
    public function send(string $recipient, string $message): void
    {
        // Integrate with SMS provider
    }
}
```

```php
class EmailNotificationGateway implements NotificationGateway
{
    public function send(string $recipient, string $message): void
    {
        // Integrate with email provider
    }
}
```

```php
class BookingReminderService
{
    public function __construct(
        private NotificationGateway $gateway
    ) {}

    public function remind(string $recipient): void
    {
        $this->gateway->send(
            $recipient,
            'Your booking is tomorrow.'
        );
    }
}
```

Additional gateways can be introduced without changing the reminder service.

Implementation selection and service-container configuration may still need to change.

### Rules

- Use polymorphism for genuinely interchangeable behavior.
- Prefer Laravel-native extension mechanisms when available.
- Avoid repeated provider-specific conditionals in core business logic.
- Do not replace simple conditionals with unnecessary class hierarchies.

## 3. Liskov Substitution Principle (LSP)

Implementations must honor the behavioral contracts they promise.

A substitute should not unexpectedly break code that depends on the contract.

### Bad Example

```php
interface WritableStorage
{
    public function write(string $path, string $content): void;
}
```

```php
class ReadOnlyStorage implements WritableStorage
{
    public function write(string $path, string $content): void
    {
        throw new RuntimeException('Writing is unsupported.');
    }
}
```

The implementation cannot provide the capability promised by the contract.

### Better Approach

Use a dedicated contract for writable storage. Read-only storage should implement a separate read-oriented contract.

### Rules

- Respect documented behavior.
- Preserve compatible preconditions and postconditions.
- Do not silently disable promised functionality.
- Use exceptions for legitimate failures, not unsupported core capabilities.
- Prefer composition when inheritance creates behavioral conflicts.

## 4. Interface Segregation Principle (ISP)

Classes should not depend on capabilities they do not use.

### Bad Example

```php
interface BookingOperations
{
    public function create(array $data): Booking;

    public function cancel(Booking $booking): void;

    public function export(): string;

    public function sendReminder(Booking $booking): void;
}
```

This combines several unrelated responsibilities.

### Better Example

```php
interface CreatesBookings
{
    public function create(array $data): Booking;
}
```

```php
interface CancelsBookings
{
    public function cancel(Booking $booking): void;
}
```

Define these contracts only when consumers benefit from depending on them.

### Rules

- Keep interfaces cohesive.
- Design contracts around consumer needs.
- Avoid large interfaces containing unrelated operations.
- Avoid creating interfaces solely because an implementation exists.

## 5. Dependency Inversion Principle (DIP)

High-level business logic should not depend unnecessarily on replaceable infrastructure details.

### Bad Example

```php
class BookingReminderService
{
    public function send(): void
    {
        $gateway = new TwilioGateway();

        $gateway->sendMessage();
    }
}
```

### Better Example

```php
class BookingReminderService
{
    public function __construct(
        private SmsGateway $gateway
    ) {}

    public function send(string $phone, string $message): void
    {
        $this->gateway->send($phone, $message);
    }
}
```

Register the implementation using Laravel's service container:

```php
$this->app->bind(
    SmsGateway::class,
    TwilioSmsGateway::class
);
```

### Rules

- Prefer constructor injection.
- Use Laravel's service container.
- Introduce interfaces at meaningful boundaries.
- Avoid unnecessary service location inside business logic.
- Do not require interfaces for every dependency.
- Concrete dependencies are acceptable when abstraction provides no benefit.

## SOLID Review Checklist

- Does each class have a clear primary responsibility?
- Can important behavior be extended without modifying stable core logic?
- Do implementations respect their contracts?
- Are interfaces appropriately focused?
- Are replaceable infrastructure details decoupled from business logic?
- Does the design remain simple?

## Final Rule

Apply SOLID to reduce complexity, not increase the number of classes.
