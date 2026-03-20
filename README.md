# Drupal Project (Lando)

This is a modern Drupal 11 development environment powered by **Lando**. It is pre-configured for high performance with PHP 8.4 and Drush 13.

## 🚀 Quick Setup

### 1. Prerequisites
Ensure you have the following installed:
* [Docker Desktop](https://www.docker.com/products/docker-desktop)
* [Lando](https://docs.lando.dev/install/)

### 2. Initialize the Project
Run these commands in order:
```bash
# Start the containers
lando start

# Install Composer dependencies (Drupal 11 + Drush 13)
lando composer install

# If you have an existing database dump
lando db-import dump.sql

# Clear Drupal cache
lando drush cr

# Apply changes made to .lando.yml.
lando rebuild
```

## Site UUID Mismatch
If your configuration import fails due to a UUID error, sync the site ID:

Open config/sync/system.site.yml and copy the uuid.
Run:
```bash

lando drush config-set system.site uuid "PASTE_UUID_HERE" -y

```

### Running Rector

**Dry run** (preview changes without modifying files):

```
vendor/bin/rector process --dry-run
```

**Apply changes** (modifies files in place):

```
vendor/bin/rector process
```

**Process a single module or directory:**

```
vendor/bin/rector process web/modules/custom/my_module
```
