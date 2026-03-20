# Architecture: purify

## Purpose

A Laravel package that wraps HTMLPurifier to provide HTML sanitisation with Laravel-specific integration: Eloquent model casts, a Facade, and Artisan commands. Protects against XSS by stripping or normalising unsafe HTML.

## Directory Structure

```
src/
  Purify.php                       - Core wrapper: delegates to HTMLPurifier, handles array input
  Purify_Manager.php               - Multi-configuration manager (supports named purifier configs)
  Purify_Service_Provider.php      - Laravel service provider; binds Purify into the container
  Facades/Purify.php               - Laravel Facade for static access
  Definitions/
    Definition.php                 - Base class for custom HTMLPurifier definition sets
    Css_Definition.php             - Extends HTMLPurifier with CSS sanitisation rules
    Html5Definition.php            - Adds HTML5 elements/attributes to HTMLPurifier's allowlist
  Casts/
    Caster.php                     - Base Eloquent cast class
    Purify_Html_On_Get.php         - Cast: sanitises on attribute read
    Purify_Html_On_Set.php         - Cast: sanitises on attribute write
  Cache/
    Cache_Definition_Cache.php     - Stores HTMLPurifier definition cache in Laravel Cache
    Filesystem_Definition_Cache.php - Stores HTMLPurifier definition cache on filesystem
  Commands/
    Clear_Command.php              - php artisan purify:clear — flushes the definition cache
```

## Key Design Decisions

- **HTMLPurifier delegation**: All actual sanitisation is performed by the battle-tested `HTMLPurifier` library; this package is purely integration glue.
- **Named configurations**: `Purify_Manager` allows multiple named HTMLPurifier configurations (e.g., strict/lenient profiles) addressable as `Purify::config('strict')->clean(...)`.
- **Cache abstraction**: Definition caching (which speeds up HTMLPurifier's first-run initialisation) is pluggable via a cache interface backed by either Laravel Cache or the filesystem.

## Extension Points

- Extend `Definition` to add custom HTML elements or attributes to the purifier allowlist.
- Implement a custom `Cache_Definition_Cache` to store definitions in Redis or another backend.

## Dependency Flow

```
Purify_Service_Provider
  └─> binds Purify_Manager into container
        └─> creates named Purify instances
              └─> Html_Purifier (external library)
Eloquent model
  └─> Purify_Html_On_Get / Purify_Html_On_Set cast
        └─> Purify::clean()
```
