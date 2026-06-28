<?php

namespace App\Support;

use App\Models\DealMeasurement;
use App\Models\DealMeasurementHistory;
use Illuminate\Support\Arr;

class DealMeasurementHistoryLogger
{
    public static function log(
        DealMeasurement $measurement,
        string $action,
        ?string $section = null,
        ?string $field = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?array $changes = null
    ): ?DealMeasurementHistory {
        if (
            $oldValue !== null &&
            $newValue !== null &&
            self::normalizeValue($oldValue) === self::normalizeValue($newValue) &&
            empty($changes)
        ) {
            return null;
        }

        return DealMeasurementHistory::create([
            'deal_measurement_id' => $measurement->id,
            'action' => $action,
            'section' => $section,
            'field' => $field,
            'old_value' => self::valueToString($oldValue),
            'new_value' => self::valueToString($newValue),
            'changes' => $changes,
            'created_by' => auth()->user()->name ?? auth()->user()->email ?? auth()->id() ?? 'System',
            'created_by_user_id' => auth()->id(),
        ]);
    }

    public static function logChanges(
        DealMeasurement $measurement,
        string $action,
        ?string $section,
        array $oldData,
        array $newData,
        array $fieldLabels = []
    ): int {
        $changes = self::diffRecursive($oldData, $newData, $fieldLabels);

        if (empty($changes)) {
            return 0;
        }

        self::log(
            measurement: $measurement,
            action: $action,
            section: $section,
            changes: $changes
        );

        return count($changes);
    }

    private static function diffRecursive(array $oldData, array $newData, array $fieldLabels = [], string $prefix = ''): array
    {
        $changes = [];

        $keys = array_unique(array_merge(
            array_keys(Arr::dot($oldData)),
            array_keys(Arr::dot($newData))
        ));

        foreach ($keys as $key) {
            $oldValue = data_get($oldData, $key);
            $newValue = data_get($newData, $key);

            if (self::normalizeValue($oldValue) === self::normalizeValue($newValue)) {
                continue;
            }

            $changes[] = [
                'field' => $key,
                'label' => $fieldLabels[$key] ?? self::niceFieldName($key),
                'old' => self::valueToString($oldValue),
                'new' => self::valueToString($newValue),
            ];
        }

        return $changes;
    }

    private static function normalizeValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return trim((string) $value);
    }

    private static function valueToString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'Ja' : 'Nein';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }

    private static function niceFieldName(string $field): string
    {
        return str($field)
            ->replace('_', ' ')
            ->replace('.', ' / ')
            ->title()
            ->toString();
    }
}