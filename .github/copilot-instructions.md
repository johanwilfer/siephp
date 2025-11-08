# GitHub Copilot Instructions

## Project Overview
Library for exporting Swedish bookkeeping data to SIE format. Built around immutable-style data classes 
(Company, Verification, Transaction) with a Dumper for SIE4 format output.

## Code Review Focus
- **Security**: Validate data encoding (CP437), prevent injection in SIE output
- **Business Logic**: Verify SIE format compliance, data validation in Domain objects
- **Type Safety**: Check strict types, proper nullable handling, array annotations
- **API Design**: Fluent interface consistency, proper exception types (DomainException)

## Do NOT Comment On
- Code style issues (handled by ECS with PSR-12, spaces, quotes)
- Type declarations & dead code (handled by Rector)
- Static analysis issues (handled by PHPStan max level + strict rules)

## Project Standards
- PHP 8.3+ strict mode with `declare(strict_types=1)`
- PHPUnit for testing
- Fluent interfaces return `self`
- Single quotes for strings (ECS enforced)
