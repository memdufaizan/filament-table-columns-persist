<?php

namespace Memdufaizan\FilamentTableColumnsPersist\Models;

use Illuminate\Database\Eloquent\Model;

class TableColumnPreference extends Model
{
    protected $table = 'table_column_preferences';

    protected $fillable = ['user_id', 'table_name', 'visible_columns'];

    protected $casts = [
        'visible_columns' => 'array',
    ];
}