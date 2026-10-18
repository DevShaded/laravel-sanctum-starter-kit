# Laravel Refactoring Guidelines

## Purpose

Improve existing Laravel code without introducing unnecessary architectural complexity or unintentionally changing behavior.

Refactor incrementally and preserve established project conventions.

## Core Rules

1. Understand the existing behavior before changing code.
2. Identify the concrete problem being addressed.
3. Make focused changes.
4. Preserve public contracts unless changes are explicitly required.
5. Respect existing Laravel conventions.
6. Avoid unrelated refactoring.
7. Do not introduce abstractions without clear benefits.
8. Follow the project's separate testing workflow when applicable.

## Step 1: Understand the Existing Code

Inspect:

- Controllers
- Models
- Relationships
- Routes
- Form Requests
- Policies
- Relevant Services and Actions
- Database constraints
- External dependencies

Identify the responsibilities and expected behavior.

Do not refactor code based on assumptions.

## Step 2: Identify Specific Problems

Look for:

- Fat controllers
- God classes
- Tight coupling
- Duplicate business rules
- Hidden side effects
- Poorly defined responsibilities
- N+1 queries
- Unnecessary service layers
- Excessive conditional logic
- Unsafe persistence operations

Prioritize problems affecting correctness, maintainability, or clarity.

## Step 3: Choose Relevant SOLID Principles

Not every refactor requires all five principles.

Choose the principles that address the actual problem.

### Example

A controller containing complex booking creation logic may benefit from SRP.

A service tightly coupled to an SMS provider may benefit from DIP.

An implementation with unsupported contract methods may violate LSP or ISP.

## Step 4: Plan the Smallest Improvement

Prefer incremental changes.

Examples:

- Extract a complex business operation into an Action.
- Move resource authorization into a Policy.
- Replace duplicated validation with a Form Request.
- Introduce an interface for an interchangeable external integration.
- Remove a repository that adds no meaningful value.

Do not rewrite entire modules when a smaller improvement is sufficient.

## Step 5: Preserve Behavior

Be careful with:

- Public API contracts
- Route names
- Request validation
- Authorization behavior
- Database constraints
- Event dispatching
- Transaction boundaries
- Queue behavior
- Exception handling
- Response formats

If behavior changes are required, identify them explicitly.

## Step 6: Improve Readability

Review:

- Class names
- Method names
- Responsibility boundaries
- Dependency clarity
- Control flow
- Domain terminology

Avoid creating excessive helper methods merely to reduce line count.

## Step 7: Check Laravel Conventions

Ensure the refactor uses Laravel features appropriately.

Prefer:

- Form Requests for substantial request validation
- Policies for resource authorization
- Eloquent relationships for model associations
- API Resources for response transformation
- Service container injection for dependencies
- Events and Jobs for suitable asynchronous or decoupled work

Do not use every feature in every implementation.

## Step 8: Review Database and Concurrency Risks

Check whether the refactor affects:

- Transactions
- Locking
- Unique constraints
- Foreign keys
- Query performance
- Eager loading
- Race conditions

Refactoring must not weaken data integrity.

## Step 9: Review the Final Design

Ask:

- Is the code easier to understand?
- Are responsibilities clearer?
- Is meaningful coupling reduced?
- Has unnecessary complexity been removed?
- Does the change follow existing conventions?
- Are any new abstractions justified?
- Has unrelated functionality been left untouched?

## Testing Boundaries

Testing is handled by a separate skill or workflow.

Do not enforce:

- TDD
- Red-Green-Refactor
- Test-first development
- Automatic test generation
- A specific testing framework

Respect existing project testing instructions.

Do not claim behavior has been verified unless appropriate verification actually occurred.

## Refactoring Response Format

When explaining a refactor, provide:

### Original Problem

Explain what was wrong and why it mattered.

### Relevant Principles

Identify which SOLID principles apply.

### Changes

Describe the important changes.

### Benefits

Explain improvements to:

- Maintainability
- Readability
- Separation of concerns
- Extensibility
- Reliability

### Trade-offs

Mention additional complexity or limitations where relevant.

## Final Rule

A successful refactor makes the code easier to maintain while preserving intended behavior.

Do not refactor merely to make code look more abstract.
