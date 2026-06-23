<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PublicWebsiteContent
{
    public static function announcements()
    {
        return DB::table('announcements')
            ->where('is_active', true)
            ->where('is_archived', false)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderBy('sort_order')
            ->orderByDesc('published_at');
    }

    public static function resources()
    {
        return DB::table('resources as r')
            ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->leftJoin('publishers as p', 'r.publisher_id', '=', 'p.id')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
            ->where('r.is_archived', false)
            ->select(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'c.category_name',
                'mt.material_type_name',
                'p.publisher_name',
                DB::raw("COALESCE(STRING_AGG(DISTINCT a.author_name, ', '), '') as authors")
            )
            ->groupBy(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'c.category_name',
                'mt.material_type_name',
                'p.publisher_name'
            )
            ->orderByDesc('r.created_at');
    }

    public static function facilities()
    {
        return DB::table('facilities')
            ->where('is_active', true)
            ->where('is_archived', false)
            ->orderBy('facility_name');
    }

    public static function onlyVisibleResources(Builder $query, string $alias = 'r'): Builder
    {
        return $query->where($alias . '.is_archived', false);
    }

    public static function onlyVisibleFacilities(Builder $query, ?string $alias = null): Builder
    {
        $prefix = $alias ? $alias . '.' : '';

        return $query
            ->where($prefix . 'is_active', true)
            ->where($prefix . 'is_archived', false);
    }

    public static function onlyVisibleAnnouncements(Builder $query, ?string $alias = null): Builder
    {
        $prefix = $alias ? $alias . '.' : '';

        return $query
            ->where($prefix . 'is_active', true)
            ->where($prefix . 'is_archived', false)
            ->where(function ($q) use ($prefix) {
                $q->whereNull($prefix . 'published_at')
                    ->orWhere($prefix . 'published_at', '<=', now());
            });
    }
}