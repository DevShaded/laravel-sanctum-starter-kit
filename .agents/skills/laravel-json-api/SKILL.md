---
name: laravel-json-api
description: "Implement this project's API resource responses with Laravel's native JSON:API resources. Use when creating or changing Eloquent resources, endpoints that serialize models or collections, or tests of those responses."
---

# Laravel JSON:API Resources

Use `Illuminate\Http\Resources\JsonApi\JsonApiResource` for this project's model resource responses. This is Laravel's native JSON:API serializer, not the ordinary `JsonResource` response format or a third-party JSON:API package.

Read the installed-version documentation through Boost `search-docs`, scoped to `laravel/framework`, before using unfamiliar behavior. The [official resource documentation](https://laravel.com/framework/docs/eloquent-resources#generating-jsonapi-resources) covers generation and the JSON:API-specific hooks. Confirm the installed framework version with `vendor/bin/sail composer show laravel/framework`.

## Create and return resources

Generate resources in `app/Http/Resources` with:

```sh
vendor/bin/sail artisan make:resource BookingResource --json-api --no-interaction
```

Follow [UserResource](../../../app/Http/Resources/UserResource.php) for a simple attribute allowlist and project PHP style. Return the resource directly from the route or controller so Laravel applies the JSON:API envelope and `Content-Type: application/vnd.api+json`.

```php
return new BookingResource($booking);

return BookingResource::collection($bookings);
```

Pass a paginator to `collection()` for paginated endpoints. Avoid manually building the envelope or wrapping the resource in `response()->json()`, which bypasses its response handling.

## Define the public contract

- List public model attributes in `$attributes`. Use `toAttributes(Request $request): array` for transformations or conditional attributes, with an accurate PHPDoc return shape. Expensive attributes can be closures so unrequested fields are not evaluated.
- Keep `id` and `type` outside the attribute list; the framework resolves them. Override `toId()` or `toType()` only when the endpoint needs a different public identity. IDs are strings. Resource type names are part of the client contract.
- Declare permitted relationships in `$relationships` or `toRelationships(Request $request): array`. Map names to resource classes when automatic discovery is insufficient. Related resources also use `JsonApiResource`.
- Clients select fields with `fields[users]=name,email` and relationships with `include=author,comments.author`. Includes produce relationship identifiers plus top-level `included` resources. Query parameters must only narrow the permitted data; they do not authorize access to attributes or related records.
- Keep credentials and private relationships outside these declarations. A model's hidden attributes do not protect fields explicitly exposed by a resource. Apply authorization and tenant constraints before returning related data, and account for eager loading when exposing collections.
- Use `toLinks()` and `toMeta()` for resource-level additions. Generate links with named routes. Keep top-level metadata separate from resource metadata.

## Scope and verification

These resources serialize responses. They do not implement incoming JSON:API write documents, filter/sort parsing, or exception formatting. Handle those only when the endpoint's requested contract includes them; do not add dependencies just to use native resources. Health checks and credential-operation payloads are not model resources.

When converting an existing endpoint, update its response tests and call out the client-visible change to `data.id`, `data.type`, and `data.attributes`. Use [ApiTokenTest](../../../tests/Feature/ApiTokenTest.php) as the current endpoint example.

Follow the project's testing skill. Cover the response's media type, identity, public attributes, and sensitive-data exclusion. Exercise sparse fields, authorized includes, and pagination when the endpoint exposes them; test the application's contract rather than duplicating Laravel's serializer suite. Keep authentication and authorization failure coverage.

Run the affected tests through Sail, then the PHP checks required by `AGENTS.md`.
