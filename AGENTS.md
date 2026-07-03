# AGENTS.md

# Project Overview

This project is a Symfony monolith using PHP 8.4.

The application exposes a REST API using API Platform.

There is no frontend.

The architecture follows Domain-Driven Design (DDD) with CQRS.

The database is PostgreSQL.

Authentication uses JWT.

Dependency Injection is configured manually. Symfony autowiring is disabled.

---

# Architecture

Each Bounded Context follows the same layered architecture.

Example:

- `src/User/Domain/`
- `src/User/Application/`
- `src/User/Infrastructure/`
- `src/User/UI/`

- `src/Access/Domain/`
- `src/Access/Application/`
- `src/Access/Infrastructure/`
- `src/Access/UI/`

Each Bounded Context should remain as independent as possible.

Avoid coupling Domain code from one Bounded Context directly to another.

Cross-context communication should happen through explicit interfaces, application services, or domain events.

---

# Dependency Rules

Dependencies always point inward.

```
UI
    ↓
Application
    ↓
Domain

Infrastructure
    ↑
```

Rules:

- UI depends on Application.
- Application depends on Domain.
- Infrastructure implements interfaces defined by Domain or Application.
- Domain must never depend on Infrastructure.
- Domain must never depend on Symfony.
- Domain must never depend on Doctrine.
- Domain must never depend on API Platform.

---

# Domain Layer

The Domain layer contains business rules and business concepts.

Allowed:

- Entities
- Value Objects
- Domain Services
- Domain Events
- Repository interfaces
- Domain exceptions
- Specifications

Not allowed:

- Controllers
- Doctrine
- SQL
- Symfony services
- HTTP
- API Platform
- External API clients
- Database implementations

Repository interfaces belong in the Domain.

Prefer Value Objects over primitive types whenever they represent business concepts.

Business invariants should be enforced inside the Domain.

---

# Application Layer

The Application layer contains use cases.

Allowed:

- Commands
- Command Handlers
- Queries
- Query Handlers
- Application DTOs
- Ports (interfaces)
- Transaction orchestration

Responsibilities:

- Coordinate Domain objects
- Call repository interfaces
- Dispatch application workflows

Application code should not:

- Access Doctrine directly
- Use EntityManager
- Contain HTTP logic
- Contain SQL
- Contain API Platform logic

---

# Infrastructure Layer

Infrastructure contains technical implementations.

Examples:

- Doctrine repositories
- Doctrine mappings
- Database access
- Messenger transports
- External service integrations
- Mail providers
- Cache implementations
- Filesystem implementations

Infrastructure may implement interfaces defined in Domain or Application.

Infrastructure is responsible for technical concerns only.

---

# UI Layer

The UI layer exposes the application through HTTP.

Allowed:

- API Platform Resources
- Symfony Controllers
- Request DTOs
- Response DTOs
- Validation
- HTTP request/response logic

Controllers and API Platform classes should remain thin.

They should delegate work to Commands or Queries.

They should never contain business logic.

---

# API Platform

API Platform is responsible only for exposing HTTP endpoints.

Business logic does not belong in:

- Resources
- Providers
- Processors
- Controllers

Business logic belongs in Application handlers or Domain objects.

---

# CQRS Rules

Commands modify state.

Queries read state.

Queries must never modify state.

Commands should not return read models.

Controllers should execute Commands or Queries instead of accessing repositories directly.

Prefer many small handlers over large generic services.

---

# Domain Events

Domain Events represent business events.

They should be created from the Domain layer.

Infrastructure is responsible for publishing them outside the Domain.

Application may react to Domain Events when coordinating workflows.

Avoid using Domain Events for technical concerns.

---

# Repository Rules

Repository interfaces belong in Domain.

Repository implementations belong in Infrastructure.

Application depends only on repository interfaces.

Never inject Doctrine EntityManager into Domain or Application.

Never access Doctrine repositories directly from Controllers.

---

# Dependency Injection

Symfony autowiring is disabled.

All dependencies must be registered manually.

Whenever a new service is created, remember to register it inside the appropriate configuration file under:

```
config/services/
```

Examples:

```
config/services/repositories/user.yaml
config/services/application/user.yaml
config/services/controllers/user.yaml
```

Do not rely on automatic service discovery.

Always verify that constructor dependencies are configured manually.

---

# Security

Authentication uses JWT.

Login and Register endpoints are public.

All other endpoints require authentication unless explicitly configured otherwise.

Respect the existing authorization model.

Do not bypass security checks.

Be especially careful when modifying:

- Authentication
- Authorization
- JWT handling
- Password hashing
- User permissions

---

# Doctrine Rules

Domain entities belong in the Domain layer.

Doctrine mappings belong in Infrastructure.

Do not move Domain entities into Infrastructure.

Avoid coupling Domain objects to Doctrine-specific APIs unless the existing architecture already requires it.

---

# Testing

PHPUnit is the testing framework.

When modifying business logic, suggest appropriate PHPUnit tests.

Prioritize tests for:

- Command Handlers
- Query Handlers
- Domain Entities
- Domain Services
- Value Objects

Do not introduce a large testing architecture without approval.

---

# Code Quality

Use the existing project configuration.

Respect:

- PHPStan
- PHPMD
- PHP-CS-Fixer

Do not introduce additional quality tools such as:

- Psalm
- Rector
- ECS

unless explicitly requested.

---

# Naming

Prefer explicit names.

Examples:

- CreateUserCommand
- CreateUserHandler
- UpdateUserCommand
- FindUserQuery
- FindUserHandler
- UserRepository

Avoid generic names such as:

- Service
- Manager
- Helper
- Utils

Class names should clearly describe their responsibility.

---

# Agent Behavior

Before editing code, explain:

- The intended change
- The files likely to be modified
- Any architectural considerations

After editing code, summarize:

- What changed
- Why it changed
- What should be tested

Prefer focused, reviewable changes.

Avoid touching unrelated files.

Small refactors are acceptable when they directly improve the requested change.

Do not perform broad architectural rewrites without approval.

---

# Avoid

Do not:

- Put business logic inside Controllers.
- Put business logic inside API Platform Resources.
- Put business logic inside Providers.
- Put business logic inside Processors.
- Put business logic inside Doctrine repositories.
- Inject EntityManager into Domain or Application.
- Access repositories directly from Controllers.
- Bypass Command or Query handlers.
- Introduce anemic Domain models.
- Introduce static service locators.
- Introduce framework dependencies into the Domain layer.

---

# Safety Rules

Ask before:

- Deleting code
- Modifying database migrations
- Changing Docker configuration
- Changing environment variables
- Making breaking API changes

Do not silently change public behavior.

Prefer backward-compatible changes whenever possible.

## Change Policy

Before modifying code:

- Explain the proposed approach.
- List the files that would be modified.
- Explain any architectural impact.

Do not edit files until the proposed approach has been approved.

Prefer discussion before implementation.
