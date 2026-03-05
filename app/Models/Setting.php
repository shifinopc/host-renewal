<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a single setting value.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::query()
            ->where('key', $key)
            ->value('value');

        return $value !== null ? $value : $default;
    }

    /**
     * Store or update a single setting value.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    /**
     * Fetch many settings at once.
     *
     * @param  array<int, string>  $keys
     * @return array<string, ?string>
     */
    public static function getMany(array $keys): array
    {
        $stored = static::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $stored[$key] ?? null;
        }

        return $results;
    }
}

