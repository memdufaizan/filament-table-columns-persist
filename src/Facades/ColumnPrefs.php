<?php

namespace Memdufaizan\FilamentTableColumnsPersist\Facades;

use Illuminate\Support\Facades\Facade;

class ColumnPrefs extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Memdufaizan\FilamentTableColumnsPersist\ColumnPrefs::class;
    }
}