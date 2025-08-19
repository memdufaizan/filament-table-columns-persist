<?php

namespace Memdufaizan\FilamentTableColumnsPersist\Traits;

use Memdufaizan\FilamentTableColumnsPersist\Facades\ColumnPrefs;

trait PersistsTableColumnToggles
{
    public function updated(string $name, $value): void
    {
        // \Log::info('Updated field: ' . $name . ', New value: ' . $value);
        
        if (str_starts_with($name, 'toggledTableColumns.')) {
            $this->persistColumnToggles();
        }
    }

    protected function persistColumnToggles(): void
    {
        ColumnPrefs::save($this->getTablePersistenceKey(), $this->toggledTableColumns ?? []);
    }

    protected function getTablePersistenceKey(): string
    {
        return property_exists($this, 'tablePersistenceKey') && $this->tablePersistenceKey
            ? $this->tablePersistenceKey
            : static::class;
    }
}