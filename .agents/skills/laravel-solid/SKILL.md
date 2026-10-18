---
name: laravel-solid
description: Apply SOLID principles, clean code, and pragmatic Laravel architecture when implementing, refactoring, reviewing, or designing Laravel applications.
---

# Laravel SOLID

Act as an experienced Laravel developer. Prioritize clean, maintainable code and Laravel-native conventions.

## Core Rules

1. Follow the project's installed PHP and Laravel versions.
2. Respect existing project conventions.
3. Apply SOLID principles pragmatically.
4. Prefer Laravel's built-in functionality.
5. Avoid unnecessary abstractions.
6. Do not automatically create interfaces or repositories.
7. Keep controllers focused on HTTP concerns.
8. Use dependency injection where beneficial.
9. Delegate testing methodology to the skills linked in the Testing section.

## Reference Routing

Read only the references relevant to the current task.

| Task | Reference |
|---|---|
| Applying SOLID principles | [SOLID](references/solid-principles.md) |
| Controllers, services, actions | [Architecture](references/laravel-architecture.md) |
| Naming and readability | [Clean Code](references/clean-code.md) |
| Eloquent and database design | [Database](references/eloquent-database.md) |
| APIs, Sanctum, authorization | [API Design](references/api-design.md) |
| Strategies and other patterns | [Design Patterns](references/design-patterns.md) |
| Detecting bad code | [Code Smells](references/code-smells.md) |
| Avoiding overengineering | [Complexity](references/complexity.md) |
| Improving existing code | [Refactoring](references/refactoring.md) |

## Workflow

1. Understand the task.
2. Inspect relevant existing code.
3. Read applicable references.
4. Choose the simplest Laravel-native solution.
5. Implement or refactor.
6. Review SOLID compliance and unnecessary complexity.

## Testing

When TDD is requested, follow the [TDD skill](../tdd/SKILL.md) for the testing workflow. Apply this skill's architecture guidance throughout implementation.

Use [Testing Best Practices](../testing-best-practices/SKILL.md) for Laravel test design and coverage, whether or not TDD is requested.

## Final Rule

Prefer the simplest correct implementation that remains readable, maintainable, and consistent with Laravel conventions.
