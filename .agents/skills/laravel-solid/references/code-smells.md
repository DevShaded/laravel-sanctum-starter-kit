# Laravel Code Smells

## Purpose

Identify structural problems that reduce readability, maintainability, correctness, or flexibility.

Do not classify code as problematic solely because it violates a stylistic preference.

## 1. Fat Controllers

### Symptoms

- Complex business rules inside controller methods
- Multiple unrelated responsibilities
- External API integrations mixed with HTTP handling
- Difficult-to-follow request workflows

### Recommended Improvement

Extract meaningful business operations into Actions or Services.

Keep request handling and response generation in controllers.

## 2. God Classes

### Symptoms

- Many unrelated methods
- Numerous dependencies
- Several independent reasons to change
- Difficult-to-understand responsibilities

### Recommended Improvement

Identify cohesive responsibilities and extract them into focused components.

Do not split classes based only on line count.

## 3. Unnecessary Repositories

### Symptoms

Repositories that only wrap:

```php
User::find($id);
User::create($data);
User::all();
```

### Recommended Improvement

Use Eloquent directly unless a repository provides a meaningful abstraction.

## 4. Interface for Every Class

### Symptoms

Every concrete class has a matching interface with no clear consumer need.

### Recommended Improvement

Introduce interfaces where they reduce meaningful coupling or support interchangeable implementations.

## 5. Excessive Service Layers

### Symptoms

Controller calls Action, Action calls Service, Service calls Repository, Repository calls Eloquent, with no meaningful behavior at intermediate layers.

### Recommended Improvement

Remove unnecessary delegation layers.

Preserve only boundaries that have a clear responsibility.

## 6. Duplicate Business Logic

### Symptoms

The same booking availability rule is implemented in multiple controllers or services.

### Recommended Improvement

Centralize the shared domain rule.

## 7. Hidden Dependencies

### Symptoms

Business logic repeatedly resolves dependencies directly from the container or global state.

### Recommended Improvement

Prefer explicit injection when dependencies matter to the class's behavior.

## 8. N+1 Queries

### Symptoms

Queries are executed for every item in a collection.

### Recommended Improvement

Use appropriate eager loading and query optimization.

## 9. Primitive Obsession

### Symptoms

Complex domain concepts are represented using loosely related strings, integers, or arrays.

### Recommended Improvement

Consider enums, value objects, or focused domain types when they protect meaningful invariants.

Do not wrap every primitive automatically.

## 10. Excessive Conditionals

### Symptoms

Large chains of provider-specific or type-specific conditional logic.

### Recommended Improvement

Consider strategies or polymorphism when behaviors are genuinely interchangeable.

Do not replace small, readable conditionals unnecessarily.

## 11. Feature Envy

### Symptoms

A class repeatedly accesses another object's internal data to implement behavior that belongs to that object.

### Recommended Improvement

Move behavior closer to the responsible component when doing so improves cohesion.

## 12. Shotgun Surgery

### Symptoms

A small business-rule change requires edits in many unrelated files.

### Recommended Improvement

Identify the actual responsibility and centralize it behind a clear boundary.

## 13. Hidden Model Side Effects

### Symptoms

Saving an Eloquent model unexpectedly triggers substantial external operations or unrelated workflows.

### Recommended Improvement

Make important workflows explicit.

Use observers and events only where their behavior remains understandable.

## 14. Unnecessary Abstractions

### Symptoms

Classes, interfaces, and factories exist primarily for hypothetical future features.

### Recommended Improvement

Remove speculative layers.

Prefer code that is simple to understand today.

## 15. Business Logic in Views or API Resources

### Symptoms

Serialization or presentation code performs domain operations or modifies application state.

### Recommended Improvement

Keep response transformation separate from business operations.

## Review Process

When identifying a code smell:

1. Describe the concrete problem.
2. Explain its impact.
3. Identify the responsible component.
4. Recommend the smallest useful improvement.
5. Consider any trade-offs.
6. Avoid unrelated refactoring.

## Final Rule

Refactor code because it has a meaningful problem, not merely because it looks different from a preferred pattern.
