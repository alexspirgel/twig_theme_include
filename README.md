# Twig Theme Include

Extends Twig core with a custom include function for proper file overrides when working with sub themes.

## Usage
```php
{{ theme_include('path/to/file.svg') }}
```

## Notes:
* Paths should be relative to the theme directory.
* If the file is not found in the active theme or its parents, the path to where the file would be in the active theme will be passed into the default twig include.
