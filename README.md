### Sample API

## Resume

This repository is a showcase project designed to demonstrate modern backend development practices and software architecture principles.

The application exposes a REST API built with PHP 8 and Symfony, following Domain-Driven Design (DDD), CQRS, and SOLID principles. The primary goal is not the business functionality itself, but rather the implementation of a clean, maintainable, scalable, and testable architecture.

### Tech Stack

- PHP 8
- Symfony
- REST API
- Domain-Driven Design (DDD)
- CQRS (Command Query Responsibility Segregation)
- SOLID Principles
- Unit & Integration Testing
- RabbitMQ (Asynchronous Messaging)
- PostgreSQL (Doctrine ORM)
- Redis (Caching & Infrastructure Services)
- Elasticsearch (Search & Indexing)
- JWT Authentication
- Docker & Docker Compose

### Architecture

The project is structured around a layered architecture with a strong separation of Bounded Context, and each Bounded Context its layered using DDD:

- Domain Layer containing business rules and domain models.
- Application Layer implementing use cases through commands and queries.
- Infrastructure Layer providing persistence, messaging, caching, and external integrations.
- API Layer exposing the application through REST endpoints.

The codebase emphasizes:

- Framework-independent domain logic.
- Dependency inversion and low coupling.
- Testability through dependency injection and clear boundaries.
- Separation between write and read operations using CQRS.
- Event Sourcing, with asynchronous processing through RabbitMQ.
- Scalable search capabilities with Elasticsearch.

### Infrastructure

The entire environment runs in Docker using isolated containers for each technology:

- PHP / Symfony Application
- PostgreSQL Database
- RabbitMQ
- Redis
- Elasticsearch

This setup mirrors a production-oriented architecture where each service is independently deployed and managed.

### Purpose

This project serves as a technical demonstration of backend engineering skills, focusing on software architecture, code quality, scalability, and maintainability rather than on a complex business domain.

It is intended as a reference implementation for developers interested in modern PHP development, enterprise application architecture, and distributed system patterns.

## Installation

Hi! These are the steps to follow

1. Download the project
```
git clone https://github.com/Jared-BB/sample-project
```
2. init the project with the magic command:
```
make start
```
---
Run the tests!
```
php bin/phpunit
```
Run tests with 100% of coverage:
```
XDEBUG_MODE=coverage php bin/phpunit tests/ --coverage-text
```
Check the code with phpstan
```
php -d memory_limit=1G vendor/bin/phpstan analyze src tests --memory-limit=1G
```
Check the code style with cs-fixer
```
vendor/bin/php-cs-fixer fix --dry-run --diff
```
Check the API Doc in a browser:
```
http://localhost:8000/api/docs
```
RabbitMQ queues can be checked in:
```
http://localhost:15672/#/
user: sample
password: pSample1
```
To connect postgres database:
```
docker exec -it sample-postgres bash
psql -U sample -d sample -W
password: pSample1
\dt
```
Check Redis:
```
docker exec -it sample-redis redis-cli
KEYS *
GET GROUP:USER:019ecb04-c6a2-7df3-b5e2-1dfda88daf4d
```
Check ElasticSearch:
```
docker exec -it sample-elasticsearch bash
curl localhost:9200/_cat/indices?v
curl -X GET "localhost:9200/users/_search?pretty"
```
