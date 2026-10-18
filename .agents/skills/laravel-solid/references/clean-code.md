# Clean Code Guidelines for Laravel

## Purpose

Write PHP and Laravel code that developers can easily understand, modify, debug, and maintain.

Readability is more important than cleverness or artificial code-size limits.

## Naming

Use names that explain business intent.

### Good

```php
$booking;
$availableSlots;
$provider;
$bookingDuration;
```

### Avoid

```php
$d;
$tmp;
$obj;
$x;
```

Use descriptive methods:

```php
calculateAvailableSlots();
cancelBooking();
confirmBooking();
```

Avoid vague names when a more specific name is available.

## Naming Priority

1. Consistency
2. Clarity
3. Domain meaning
4. Specificity
5. Brevity

Use the same terminology throughout the application.

## Methods

Methods should:

- Have a clear purpose.
- Express their intent.
- Avoid unnecessary nesting.
- Minimize unexpected side effects.
- Use meaningful parameters.
- Return predictable results.

Do not enforce arbitrary method-length limits.

### Guard Clauses

Prefer guard clauses when they simplify control flow.

```php
public function cancel(Booking $booking): void
{
    if ($booking->status === BookingStatus::Completed) {
        throw new DomainException(
            'Completed bookings cannot be cancelled.'
        );
    }

    $booking->update([
        'status' => BookingStatus::Cancelled,
    ]);
}
```

This is a simplified example. Real cancellation logic must also enforce authorization and the full set of valid status transitions.

## Classes

Classes should have cohesive responsibilities.

Avoid:

- God classes
- Unrelated methods
- Excessive constructor dependencies
- Hidden dependencies
- Unnecessary inheritance

Prefer composition when appropriate.

Do not enforce arbitrary class line-count or property-count restrictions.

## Type Safety

Use PHP types where supported:

```php
public function calculateTotal(
    int $price,
    int $quantity
): int {
    return $price * $quantity;
}
```

Use:

- Parameter types
- Return types
- Nullable types
- Typed properties
- Enums
- Readonly properties where appropriate

Respect the installed PHP version.

## Enums

Use enums for meaningful fixed domain states.

```php
enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

Prefer enums over scattered string comparisons when they clarify the domain.

## Value Objects

Consider value objects for domain concepts requiring invariants.

Examples:

- Money
- DateRange
- TimeSlot
- Percentage

Do not create value objects for every ID, email, or scalar automatically.

## Error Handling

- Throw meaningful exceptions.
- Avoid swallowing exceptions silently.
- Avoid generic exception handling without a purpose.
- Preserve useful debugging information.
- Keep HTTP-specific responses at appropriate application boundaries.

Do not expose sensitive internal details in production API responses.

## Comments

Prefer self-explanatory code.

Add comments when explaining:

- Non-obvious business constraints
- Complex algorithms
- External system limitations
- Concurrency decisions
- Architectural trade-offs

Avoid comments that simply repeat the code.

## Duplication

Use DRY pragmatically.

Extract duplicated business rules.

Do not merge unrelated operations solely because their implementations look similar.

## Conditionals

Use early returns when they improve readability.

Avoid deeply nested conditionals.

Use polymorphism when repeated conditionals represent genuinely interchangeable behavior.

Do not replace every conditional with a design pattern.

## Dependencies

Prefer explicit dependencies.

Avoid hidden infrastructure dependencies when injection would provide a clear benefit.

## Final Checklist

- Are names meaningful?
- Are responsibilities clear?
- Is the control flow understandable?
- Are types appropriate?
- Are side effects explicit?
- Is duplication meaningful?
- Can the code be simpler?

## Final Rule

Readable, straightforward Laravel code is preferable to clever or unnecessarily abstract code.
