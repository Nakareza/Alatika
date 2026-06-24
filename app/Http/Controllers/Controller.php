<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Read keperluan options from storage with same_day flag.
     * Automatically migrates old flat-array format to new structure.
     */
    protected static function getKeperluanOptions(): array
    {
        $path = storage_path('app/keperluan.json');
        $sameDayDefaults = ['Tugas Harian', 'Praktikum', 'Perkuliahan'];

        if (!file_exists($path)) {
            $defaults = [
                ['name' => 'Penelitian', 'same_day' => false],
                ['name' => 'Tugas Harian', 'same_day' => true],
                ['name' => 'Pengabdian', 'same_day' => false],
                ['name' => 'Praktikum', 'same_day' => true],
                ['name' => 'Perkuliahan', 'same_day' => true],
            ];
            file_put_contents($path, json_encode($defaults, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return $defaults;
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            return [];
        }

        // Backward compatibility: migrate old flat array of strings
        if (!empty($data) && is_string(reset($data))) {
            $data = array_map(fn($name) => [
                'name' => $name,
                'same_day' => in_array($name, $sameDayDefaults),
            ], $data);
            file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        return $data;
    }

    /**
     * Write keperluan options back to storage.
     */
    protected static function saveKeperluanOptions(array $options): void
    {
        $path = storage_path('app/keperluan.json');
        file_put_contents($path, json_encode($options, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * Get flat list of keperluan names only (for backward compatibility where needed).
     */
    protected static function getKeperluanNames(): array
    {
        return array_column(static::getKeperluanOptions(), 'name');
    }

    /**
     * Check if a given keperluan name is same-day return.
     */
    protected static function isKeperluanSameDay(string $name): bool
    {
        foreach (static::getKeperluanOptions() as $opt) {
            if ($opt['name'] === $name) {
                return (bool) $opt['same_day'];
            }
        }
        return false;
    }

    /**
     * Add a new keperluan name to the global list if it doesn't already exist.
     */
    protected static function addKeperluanIfNew(string $name): void
    {
        $options = static::getKeperluanOptions();
        $exists = in_array($name, array_column($options, 'name'), true);

        if (!$exists && trim($name) !== '') {
            $options[] = ['name' => trim($name), 'same_day' => false];
            static::saveKeperluanOptions($options);
        }
    }
}
