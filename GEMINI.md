# Project Context & Guidelines: ProjectDrupal

This file provides foundational mandates for AI-assisted development within this repository. Adhere to these rules for all tasks.

## Project Overview
- **Core:** Drupal 11 (Standard installation).
- **Environment:** Lando (`.lando.yml`), Travis CI (`.travis.yml`).
- **Configuration Management:** Stored in `config/sync`.
- **Key Features:** Extensive use of **Drupal Commerce** (Products, Orders, Shipping, Payments).
- **Contrib Ecosystem:** `search_api`, `ai`, `ai_generation`, `migrate_tools`, `hook_event_dispatcher`.

## Mandatory Coding Standards
1. **OOP Hooks (Drupal 11 Style):**
   - **DO NOT** use global functions in `.module` files for hooks.
   - **USE** the `#[Hook]` attribute in classes located in `src/Hook/`.
   - Implement `ContainerInjectionInterface` for all Hook classes to inject services.
2. **Service Injection:**
   - Always prefer Constructor Injection via the `create()` method.
   - Avoid `\Drupal::service()` or `\Drupal::messenger()` calls inside classes.
3. **Type Safety:**
   - Use PHP 8.3+ features (strict types, property promotion, return types).
   - Ensure all `NodeInterface` or other Entity interfaces are properly imported.
4. **Documentation:**
   - Every class and method must have proper DocBlocks.
   - Use `@inheritdoc` when implementing standard Drupal interfaces.

## Project Structure (Reference)
- `web/modules/custom/`: Primary location for custom logic.
- `web/modules/contrib/`: Do not modify files here.
- `config/sync/`: Target for all configuration exports/imports.
- `vendor/bin/drush`: Path to the local Drush binary.

## Operational Rules
- **Drush:** Always use `./vendor/bin/drush` for commands.
- **Testing:** When adding features to custom modules, look for existing tests in `tests/src/Functional` or `tests/src/Kernel` within the module.
- **Clean Code:** Remove unused imports and maintain PSR-12 compliance.
