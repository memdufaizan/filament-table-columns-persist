<?php

namespace Memdufaizan\FilamentTableColumnsPersist\Traits;

use Memdufaizan\FilamentTableColumnsPersist\Facades\ColumnPrefs;

trait PersistsTableColumnToggles
{
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