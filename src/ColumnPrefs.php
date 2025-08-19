<?php

namespace Memdufaizan\FilamentTableColumnsPersist;

use Illuminate\Support\Facades\Auth;
use Memdufaizan\FilamentTableColumnsPersist\Models\TableColumnPreference;

class ColumnPrefs
{
    public function normalize(string $table): string
    {
        return str_contains($table, '.') ? $table : "{$table}.table";
    }

    public function defaults(string $table): array
    {
        $map = config('ftcp.defaults', []);
        return $map[$table] ?? [];
    }

    public function visible(string $table, ?int $userId = null, ?array $fallback = null): array
    {
        $key = $this->normalize($table);
        $userId = $userId ?? $this->resolveUserId();

        if (! $userId) {
            return $fallback ?? $this->defaults($table);
        }

        $prefs = TableColumnPreference::query()
            ->where('user_id', $userId)
            ->where('table_name', $key)
            ->first();

        $cols = $prefs?->visible_columns;

        if (is_string($cols)) {
            $decoded = json_decode($cols, true);
            $cols = is_array($decoded) ? $decoded : null;
        }

        return is_array($cols)
            ? array_values(array_unique(array_map('strval', $cols)))
            : ($fallback ?? $this->defaults($table));
    }

    public function save(string $table, array $toggles, ?int $userId = null): array
    {
        $visible = $this->flatten($toggles);
        $key = $this->normalize($table);
        $userId = $userId ?? $this->resolveUserId();

        if ($userId) {
            TableColumnPreference::updateOrCreate(
                ['user_id' => $userId, 'table_name' => $key],
                ['visible_columns' => $visible],
            );
        }

        return $visible;
    }

    public function isVisible(string $table, string $name, ?int $userId = null, ?array $fallback = null): bool
    {
        return in_array($name, $this->visible($table, $userId, $fallback), true);
    }

    public function flatten(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}.{$key}";
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten($value, $fullKey));
            } elseif ($value === true) {
                $result[] = $fullKey;
            }
        }
        return $result;
    }

    protected function resolveUserId(): ?int
    {
        return Auth::guard(config('ftcp.guard', 'filament'))->id()
            ?? Auth::id();
    }
}