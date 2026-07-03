# Create API Endpoint

Use this skill whenever a new API endpoint must be implemented.

The caller should describe the endpoint's business behavior, not the implementation details.

Before writing code:

1. Summarize the requested endpoint.
2. List the files that will be created or modified.
3. Explain the proposed architecture.
4. Wait for approval before making changes.

Do not modify files until the user explicitly approves the implementation.

---

# Project Architecture

The project uses:

- Symfony
- API Platform
- PHP 8.4
- DDD
- CQRS
- PostgreSQL
- Doctrine ORM
- Messenger
- LexikJWTAuthenticationBundle

There is no frontend.

Dependency Injection is configured manually.

Symfony autowiring is disabled.

Most endpoints require JWT authentication.

Only Login and Register are public unless explicitly requested.

---

# Endpoint Checklist

For every endpoint, determine:

- Bounded Context
- HTTP method
- URI
- Read or Write operation
- Authentication requirements
- Required permissions
- Request payload
- Response payload
- Business logic
- Persistence changes
- Functional test scenarios

If information is missing, make reasonable assumptions and clearly state them.

---

# Step 1 — Configure API Platform

Update:

```text
config/api_platform/{bounded_context}.yaml
```

Create a new operation.

Use:

- Processor for write operations.
- Provider for read operations.

Keep the configuration declarative.

Do not place business logic inside API Platform configuration.

---

# Step 2 — Register Services

Register the new Processor or Provider manually.

Use:

```text
config/services/api/{bounded_context}.yaml
```

Remember:

- Autowiring is disabled.
- Every constructor dependency must be configured manually.

Never assume Symfony will discover new services automatically.

---

# Step 3 — Request DTO

If the endpoint accepts input:

Create:

```text
src/{BoundedContext}/UI/{Action}/Request/
```

Rules:

- Use Symfony Validator.
- Only validate transport concerns.
- Do not implement business rules.
- Business validation belongs to the Domain.

---

# Step 4 — Response DTO

If the endpoint returns data:

Create:

```text
src/{BoundedContext}/UI/{Action}/Response/
```

Rules:

- Never expose Domain entities.
- Never expose Doctrine entities.
- Never expose sensitive fields.
- Response DTOs represent the public API only.

---

# Step 5 — Processor or Provider

Write operations use:

- Processor
- Command
- commands.bus

Read operations use:

- Provider
- Query
- queries.bus

Processors and Providers should remain thin.

They should only:

- Validate transport concerns.
- Build Commands or Queries.
- Dispatch them through Messenger.
- Transform responses when necessary.

They must not contain business logic.

---

# Step 6 — Authorization

Determine whether the endpoint requires authorization.

If so:

Use the AccessGuard of the corresponding Bounded Context.

Typical example:

```php
$this->accessGuard->isGranted(actionPermission: Permission::LIST);
```

If object-level authorization is required, pass the object identifier.

Do not duplicate permission logic.

Do not bypass the existing AccessGuard/Voter model.

---

# Step 7 — Command or Query

Write operations create:

```text
Application/Command/
```

Read operations create:

```text
Application/Query/
```

Rules:

- Commands modify state.
- Queries never modify state.
- They are immutable DTOs.
- They contain no business logic.

---

# Step 8 — Handler

Create:

- CommandHandler
- QueryHandler

under:

```text
Application/
```

Responsibilities:

- Orchestrate the use case.
- Load aggregates.
- Call Domain behavior.
- Persist through repository interfaces.
- Raise Domain Events when appropriate.

Handlers must not:

- Contain HTTP logic.
- Use Doctrine EntityManager directly.
- Contain business invariants.

---

# Step 9 — Register Handler

Register manually:

Commands:

```text
config/services/command_handlers/{bounded_context}.yaml
```

Queries:

```text
config/services/query_handlers/{bounded_context}.yaml
```

Use the correct Messenger bus.

Never rely on autowiring.

---

# Step 10 — Domain Changes

If the endpoint requires new business behavior:

Modify the Domain.

Possible changes include:

- Entities
- Aggregate Roots
- Value Objects
- Domain Services
- Domain Events
- Repository interfaces

Rules:

- Respect Aggregate boundaries.
- Preserve invariants.
- Keep business rules inside the Domain.
- Persist through repository interfaces.

---

# Step 11 — Functional Tests

Create functional endpoint tests.

Tests must exercise the API, not internal classes.

Prioritize functional coverage over unit coverage.

---

# Functional Test Matrix

Every endpoint should consider:

## Authentication

- authenticated request
- unauthenticated request
- invalid JWT

## Authorization

- sufficient permission
- insufficient permission
- object-level permission (if applicable)

## Validation

- valid request
- missing required fields
- invalid formats
- invalid values

## Business

- success case
- duplicate resource
- resource not found
- invalid state transition
- expected side effects

## Response

- status code
- response body
- hidden sensitive fields

## Persistence

- database updated correctly
- database unchanged after failure

## Read Endpoints

When applicable:

- pagination
- filtering
- searching
- sorting
- empty results

---

# Final Checklist

Before considering the endpoint complete, verify:

- API Platform operation created.
- Request DTO created (if needed).
- Response DTO created (if needed).
- Processor or Provider created.
- Processor or Provider registered manually.
- Command or Query created.
- Handler created.
- Handler registered manually.
- AccessGuard used when required.
- Domain changes completed.
- Repository interfaces respected.
- Domain Events raised when appropriate.
- Functional tests added.
- Corner cases covered.
- No business logic leaked into UI or Infrastructure.

---

# Completion

When finished:

- Summarize what was created.
- Mention every modified file.
- Mention any manual follow-up required.
- Suggest additional functional tests if coverage can be improved.
