### Sample API

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
