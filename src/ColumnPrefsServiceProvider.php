<?php

namespace Memdufaizan\FilamentTableColumnsPersist;

use Illuminate\Support\ServiceProvider;

class ColumnPrefsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ftcp.php', 'ftcp');

        $this->app->singleton(ColumnPrefs::class, fn () => new ColumnPrefs());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/ftcp.php' => config_path('ftcp.php'),
        ], 'ftcp-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_table_column_preferences_table.php' =>
                database_path('migrations/' . date('Y_m_d_His') . '_create_table_column_preferences_table.php'),
        ], 'ftcp-migrations');
    }
}