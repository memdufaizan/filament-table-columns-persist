# Filament Table Columns Persist

Persist Filament table column visibility per user, with sensible defaults. Works with Filament Tables v3 on Laravel 10/11.

- Saves per-user visible columns for any table key you choose
- Provides default columns when no preference exists
- Simple Facade API and an optional trait to persist toggles

## Requirements

- PHP >= 8.1
- Laravel 10 or 11
- Filament Tables ^3.0

## Install (from GitHub, not on Packagist yet)

1) Add the GitHub repository to your app’s composer.json:
```json
{
  "repositories": [
    { "type": "vcs", "url": "https://github.com/memdufaizan/filament-table-columns-persist" }
  ]
}
```

2) Require the package (choose one):
```bash
# If you have a release tag (recommended)
composer require memdufaizan/filament-table-columns-persist:^1.0

# Or install the main branch
composer require memdufaizan/filament-table-columns-persist:dev-main
```

If Composer complains about stability, you can either use a tagged version, or temporarily set:
```json
{
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

3) Publish config and migration, then migrate:
```bash
php artisan vendor:publish --tag=ftcp-config
php artisan vendor:publish --tag=ftcp-migrations
php artisan migrate
```

## Configure defaults

Defaults are used when a user has not saved any preference yet. Edit config/ftcp.php:
```php
return [
    'guard' => 'web',

    'defaults' => [
        'students' => [
            'roll_no',
            'student_name',
            'class_name',
            'section_name',
            'photo',
        ],
    ],
];
```

Note: The package normalizes your key to "<key>.table" internally. Using 'students' is fine.

## Persisting toggles (Table modal)

Add the trait to your Filament List page and call persist on toggle updates.

Example (ListStudents page):
```php
<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Username\Change\To\Your\Namespace; // ...existing code...
use Memdufaizan\FilamentTableColumnsPersist\Traits\PersistsTableColumnToggles;

class ListStudents extends ListRecords
{
    use PersistsTableColumnToggles;

    protected string $tablePersistenceKey = 'students';

    // Livewire v3: watch for toggle changes and persist
    public function updated(string $name, $value): void
    {
        if (str_starts_with($name, 'toggledTableColumns.')) {
            $this->persistColumnToggles();
        }
    }
}
```

Alternatively, you can persist manually anywhere:
```php
use Memdufaizan\FilamentTableColumnsPersist\Facades\ColumnPrefs;

// $this->toggledTableColumns comes from Filament Table state
ColumnPrefs::save('students', $this->toggledTableColumns ?? []);
```

## Reading visibility in your Resource

Use the Facade to get the visible columns list (with defaults), then decide per column.

Example (StudentResource table):
```php
use Memdufaizan\FilamentTableColumnsPersist\Facades\ColumnPrefs;
use Filament\Tables\Columns\TextColumn;

// Defaults + current user's saved list:
$defaults = ColumnPrefs::defaults('students');
$visible = ColumnPrefs::visible('students', null, $defaults);

// Helper:
$isVisible = fn (string $name) => in_array($name, $visible, true);

// Usage in columns:
TextColumn::make('roll_no')
    ->label(__('Roll No.'))
    ->toggleable(isToggledHiddenByDefault: ! $isVisible('roll_no'));

TextColumn::make('student_name')
    ->label(__('Student Name'))
    ->toggleable(isToggledHiddenByDefault: ! $isVisible('student_name'));

// ...and so on for each column
```

You can also check directly:
```php
ColumnPrefs::isVisible('students', 'roll_no', userId: null, fallback: $defaults);
```

## How it works

- User toggles are flattened into dot keys and stored in table_column_preferences as JSON per user + table key.
- When reading, the package returns the saved list or your configured defaults.

DB table (created by migration):
- id, user_id, table_name, visible_columns (json), timestamps
- Unique index on [user_id, table_name]

## Troubleshooting

- Class "Memdufaizan\FilamentTableColumnsPersist\ColumnPrefsServiceProvider" not found:
  - Run: composer dump-autoload -o
  - Run: php artisan package:discover
  - Ensure vendor/memdufaizan/filament-table-columns-persist/src/ColumnPrefsServiceProvider.php exists and namespace matches.
  - If you just pushed a fix, require the new tag or use dev-main. Clear cache: composer clear-cache

- Stability errors when installing from GitHub:
  - Require a tag (e.g., ^1.0), or use dev-main with "minimum-stability": "dev" and "prefer-stable": true.

- GitHub API rate limit:
  - composer config -g github-oauth.github.com YOUR_GITHUB_TOKEN

## License

MIT