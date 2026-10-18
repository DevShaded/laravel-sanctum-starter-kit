# Laravel Eloquent and Database Guidelines

## Purpose

Design reliable database structures and efficient Eloquent queries while preserving Laravel conventions.

Prioritize data integrity, query efficiency, and maintainability.

## Eloquent Models

Use Eloquent models for:

- Relationships
- Attribute casting
- Query scopes
- Relevant entity behavior
- Persistence through Laravel's ORM

Do not automatically introduce repositories around Eloquent.

## Relationships

Prefer proper Eloquent relationships over repeated manual joins.

Common relationships:

- belongsTo
- hasOne
- hasMany
- belongsToMany
- morphOne
- morphMany
- morphTo

Use the relationship that matches the actual domain.

### Example

```php
class Booking extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'provider_id'
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

Add the required namespace imports in actual implementations.

## Prevent N+1 Queries

Bad:

```php
$bookings = Booking::all();

foreach ($bookings as $booking) {
    echo $booking->provider->name;
}
```

Better:

```php
$bookings = Booking::with('provider')->get();
```

Use eager loading when relationships are needed.

Avoid loading unnecessary relationships.

## Query Efficiency

- Select only necessary columns when useful.
- Use pagination for large result sets.
- Avoid queries inside loops.
- Use database filtering rather than unnecessary PHP collection filtering.
- Use `exists()` for existence checks.
- Use `count()` for database-level counting.
- Consider `chunk()`, `chunkById()`, or `cursor()` for large datasets.

Example:

```php
$hasBookings = Booking::query()
    ->where('provider_id', $providerId)
    ->exists();
```

## Scopes

Use query scopes for meaningful reusable query logic.

```php
public function scopeConfirmed(
    Builder $query
): void {
    $query->where(
        'status',
        BookingStatus::Confirmed
    );
}
```

Avoid creating a scope for every trivial condition.

## Migrations

- Use descriptive column names.
- Add appropriate foreign keys.
- Use meaningful indexes.
- Use unique constraints where applicable.
- Choose appropriate column types.
- Consider deletion behavior.
- Preserve historical data when required.

## Indexes

Create indexes based on real query patterns.

For a booking application, frequent queries may filter by:

- Provider
- Status
- Start time
- Customer

Consider composite indexes when they match actual queries.

Do not add indexes indiscriminately.

## Transactions

Use database transactions when operations must succeed or fail together.

```php
DB::transaction(function () use ($data) {
    $booking = Booking::create($data);

    BookingHistory::create([
        'booking_id' => $booking->id,
        'status' => $booking->status,
    ]);
});
```

Transactions provide atomicity, but do not automatically prevent every concurrency issue.

## Concurrency

For booking systems, inventory, and similar domains:

- Identify shared resources.
- Protect critical invariants.
- Use appropriate transaction isolation, locking, or constraints.
- Ensure competing requests cannot both commit conflicting changes.
- Consider deadlocks and retry behavior.
- Do not rely only on an application-level availability query.

For booking creation, checking for overlaps before saving is insufficient without an appropriate concurrency-control strategy.

## Mass Assignment

Only persist approved fields.

Prefer validated data:

```php
$booking = Booking::create(
    $request->validated()
);
```

This is appropriate only when validated fields are also authorized for assignment.

Do not let customers supply protected fields such as another customer's ID, elevated roles, or privileged booking statuses.

Derive ownership values from authenticated context when appropriate.

## Soft Deletes

Use soft deletes when they match the business requirements.

Consider implications for:

- Historical records
- Unique constraints
- Relationships
- Queries
- Data retention

Do not enable soft deletes on every model automatically.

## Timezones

For applications involving appointments or bookings:

- Define a timezone strategy.
- Store and compare timestamps consistently.
- Convert times at application boundaries where appropriate.
- Consider daylight-saving transitions.
- Distinguish local wall-clock availability from absolute timestamps.

## Final Rule

Correctness and database integrity come before query convenience or architectural abstraction.
