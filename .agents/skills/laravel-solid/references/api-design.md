# Laravel REST API Design

## Purpose

Build consistent, secure, maintainable REST APIs using Laravel conventions.

## Routes

Prefer resource-oriented endpoints.

```text
GET    /api/bookings
POST   /api/bookings
GET    /api/bookings/{booking}
PATCH  /api/bookings/{booking}
DELETE /api/bookings/{booking}
```

Use explicit action endpoints when they better describe meaningful domain operations.

```text
PATCH /api/bookings/{booking}/confirm
PATCH /api/bookings/{booking}/cancel
```

## Controllers

Keep API controllers focused on:

- Request handling
- Delegating application behavior
- Returning responses

Avoid complex business logic and infrastructure integrations in controllers.

## Validation

Use Laravel validation.

For substantial validation, prefer Form Requests.

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

Business constraints must also be enforced inside the operation where they can be checked consistently.

## Authentication

Use Laravel's supported authentication mechanisms.

For APIs using Sanctum:

- Protect endpoints with appropriate authentication middleware.
- Validate credentials securely.
- Apply appropriate token lifecycle rules.
- Revoke tokens when logging out in token-based flows.
- Do not expose tokens in logs or API responses unintentionally.

Sanctum supports different authentication approaches. Respect the application's existing session-based or token-based architecture.

## Authorization

Use:

- Policies
- Gates
- Middleware
- Form Request authorization where appropriate

Prefer resource-level Policies for resource operations.

### Examples

- Customers access their own bookings.
- Providers manage assigned bookings.
- Admins manage services.

Do not trust user IDs, roles, or ownership claims supplied by request bodies.

## API Resources

Use API Resources when a dedicated response representation is helpful.

```php
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
        ];
    }
}
```

Avoid exposing sensitive data.

Prevent N+1 queries when serializing related resources.

## HTTP Status Codes

Use appropriate status codes:

- 200: Successful request
- 201: Resource created
- 204: Successful request without response body
- 400: Malformed request
- 401: Unauthenticated
- 403: Forbidden
- 404: Not found
- 409: Conflict
- 422: Validation error
- 429: Too many requests
- 500: Unexpected server failure

## Error Handling

Return consistent error structures.

Do not expose:

- Stack traces
- Database credentials
- Internal tokens
- Sensitive implementation details

Use Laravel's exception handling mechanisms.

Keep domain failures separate from unexpected infrastructure failures.

## Filtering and Pagination

Use query parameters for resource filtering.

Example:

```text
GET /api/bookings?status=confirmed&page=2
```

Validate supported filters.

Prefer pagination for potentially large collections.

## API Security

- Enforce authorization server-side.
- Validate input.
- Prevent mass-assignment vulnerabilities.
- Apply rate limiting where appropriate.
- Use HTTPS in deployed environments.
- Protect secrets.
- Avoid leaking sensitive fields.
- Handle token revocation correctly.
- Consider idempotency for operations where retries may create duplicates.

## Final Rule

Use Laravel's API features before building custom infrastructure.
