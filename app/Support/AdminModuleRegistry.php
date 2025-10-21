<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdminModuleRegistry
{
    public static function views(): array
    {
        return config('admin_modules.views', []);
    }

    public static function view(string $key): ?array
    {
        return self::views()[$key] ?? null;
    }

    public static function modules(): array
    {
        return config('admin_modules.modules', []);
    }

    public static function module(string $key): ?array
    {
        return self::modules()[$key] ?? null;
    }

    public static function moduleOrder(): array
    {
        return config('admin_modules.module_order', array_keys(self::modules()));
    }

    public static function validViewKeys(): array
    {
        return array_keys(self::views());
    }

    public static function labelForView(string $view): string
    {
        $def = self::view($view);
        if ($def && isset($def['label'])) {
            return $def['label'];
        }

        return Str::of($view)->replace(['-', '_'], ' ')->trim()->title();
    }

    public static function permissionCandidates(string $view): array
    {
        $def = self::view($view);
        if (!$def) {
            return [];
        }

        return Arr::wrap($def['objects'] ?? []);
    }

    public static function moduleKeyForView(string $view): ?string
    {
        $def = self::view($view);
        return $def['module'] ?? null;
    }

    public static function moduleKeyForObjectName(?string $objectName): ?string
    {
        if (!$objectName) {
            return null;
        }

        $needle = self::normalizeLabel($objectName);

        foreach (self::modules() as $key => $module) {
            foreach (Arr::wrap($module['objects'] ?? []) as $candidate) {
                if ($needle === self::normalizeLabel($candidate)) {
                    return $key;
                }
            }
        }

        foreach (self::views() as $view) {
            $moduleKey = $view['module'] ?? null;
            if (!$moduleKey) {
                continue;
            }
            foreach (Arr::wrap($view['objects'] ?? []) as $candidate) {
                if ($needle === self::normalizeLabel($candidate)) {
                    return $moduleKey;
                }
            }
        }

        return null;
    }

    public static function modulesForFrontend(): array
    {
        $modules = self::modules();
        $views = self::views();
        $order = self::moduleOrder();

        $grouped = [];
        foreach ($modules as $key => $module) {
            $label = $module['label'] ?? Str::of($key)->replace('-', ' ')->title();
            $grouped[$key] = [
                'key' => $key,
                'label' => $label,
                'object_names' => array_values(array_unique(Arr::wrap($module['objects'] ?? []))),
                'submodules' => [],
            ];
        }

        foreach ($views as $viewKey => $viewDef) {
            $moduleKey = $viewDef['module'] ?? null;
            if (!$moduleKey || !isset($grouped[$moduleKey])) {
                continue;
            }
            if (($viewDef['type'] ?? 'partial') !== 'partial') {
                continue;
            }
            $grouped[$moduleKey]['submodules'][] = [
                'view' => $viewKey,
                'label' => $viewDef['label'] ?? self::labelForView($viewKey),
                'object_names' => array_values(array_unique(Arr::wrap($viewDef['objects'] ?? []))),
            ];
        }

        $ordered = [];
        foreach ($order as $moduleKey) {
            if (isset($grouped[$moduleKey])) {
                $moduleData = $grouped[$moduleKey];
                $moduleData['submodules'] = self::sortSubmodules($moduleData['submodules']);
                $ordered[] = $moduleData;
            }
        }

        // Append any modules not referenced in order
        foreach ($grouped as $key => $moduleData) {
            if (!in_array($key, $order, true)) {
                $moduleData['submodules'] = self::sortSubmodules($moduleData['submodules']);
                $ordered[] = $moduleData;
            }
        }

        return $ordered;
    }

    protected static function sortSubmodules(array $submodules): array
    {
        return Collection::make($submodules)
            ->sortBy(fn($item) => Str::lower($item['label'] ?? ''))
            ->values()
            ->all();
    }

    protected static function normalizeLabel(string $value): string
    {
        return Str::of($value)->ascii()->lower()->replaceMatches('/\s+/', ' ')->trim();
    }
}
