<?php

namespace App\Services;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GeoCacheService
{
    protected const int TTL = 3600;

    /**
     * Get all provinces, optionally filtered by search term.
     *
     * @param  array{search?: string}  $filters
     */
    public static function getProvinces(array $filters = []): Collection
    {
        $key = static::buildKey('provinces', $filters);

        return Cache::remember($key, static::TTL, function () use ($filters) {
            return Province::query()
                ->when($filters['search'] ?? null, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->orderBy('name')
                ->get(['id', 'name']);
        });
    }

    /**
     * Get all regencies, optionally filtered by search term or province_id.
     *
     * @param  array{search?: string, province_id?: int|string}  $filters
     */
    public static function getRegencies(array $filters = []): Collection
    {
        $key = static::buildKey('regencies', $filters);

        return Cache::remember($key, static::TTL, function () use ($filters) {
            return Regency::query()
                ->when($filters['search'] ?? null, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($filters['parent_id'] ?? null, fn ($query, $parentId) => $query
                    ->where('province_id', $parentId))
                ->orderBy('name')
                ->get(['id', 'name']);
        });
    }

    /**
     * Get all districts, optionally filtered by search term or regency_id.
     *
     * @param  array{search?: string, regency_id?: int|string}  $filters
     */
    public static function getDistricts(array $filters = []): Collection
    {
        $key = static::buildKey('districts', $filters);

        return Cache::remember($key, static::TTL, function () use ($filters) {
            return District::query()
                ->when($filters['search'] ?? null, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($filters['parent_id'] ?? null, fn ($query, $parentId) => $query
                    ->where('regency_id', $parentId))
                ->orderBy('name')
                ->get(['id', 'name']);
        });
    }

    /**
     * Get all villages, optionally filtered by search term or district_id.
     *
     * @param  array{search?: string, district_id?: int|string}  $filters
     */
    public static function getVillages(array $filters = []): Collection
    {
        $key = static::buildKey('villages', $filters);

        return Cache::remember($key, static::TTL, function () use ($filters) {
            return Village::query()
                ->when($filters['search'] ?? null, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($filters['parent_id'] ?? null, fn ($query, $parentId) => $query
                    ->where('district_id', $parentId))
                ->orderBy('name')
                ->get(['id', 'name']);
        });
    }

    /**
     * Flush all geographic data from the cache.
     */
    public static function flush(): void
    {
        $prefixes = ['provinces', 'regencies', 'districts', 'villages'];

        foreach ($prefixes as $prefix) {
            $key = 'geo:'.static::buildKey($prefix, []);

            // Use the cache store to forget the base key
            Cache::forget($key);
        }

        // Since we can't easily enumerate all keys with dynamic filters,
        // we also rely on the application cache:clear in SettingController
        // to handle all cached keys.
    }

    /**
     * Build a cache key for the given resource and filters.
     */
    protected static function buildKey(string $resource, array $filters): string
    {
        $hash = md5(serialize($filters));

        return "geo:{$resource}:{$hash}";
    }
}
