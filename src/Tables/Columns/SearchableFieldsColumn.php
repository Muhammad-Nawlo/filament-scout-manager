<?php

namespace MuhammadNawlo\FilamentScoutManager\Tables\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SearchableFieldsColumn extends Column
{
    protected string $view = 'filament-scout-manager::tables.columns.searchable-fields';

    public function getState(): mixed
    {
        $record = $this->getRecord();
        $modelClass = data_get($record, 'class', $record);

        try {
            if ($modelClass instanceof Model) {
                $model = $modelClass;
            } elseif (is_string($modelClass) && class_exists($modelClass)) {
                $model = new $modelClass;
            } else {
                return [];
            }

            if (method_exists($model, 'toSearchableArray')) {
                $example = $model->toSearchableArray();

                return array_keys($example);
            }

            if (method_exists($model, 'getFillable') && ! empty($model->getFillable())) {
                return $model->getFillable();
            }

            $table = $model->getTable();

            return Schema::getColumnListing($table);
        } catch (\Exception) {
            return [];
        }
    }

    public function getFormattedState()
    {
        $fields = $this->getState();

        if (empty($fields)) {
            return __('filament-scout-manager::filament-scout-manager.models.fields.no_fields_configured');
        }

        $displayFields = array_slice($fields, 0, 3);
        $remaining = count($fields) - 3;

        $html = '';
        foreach ($displayFields as $field) {
            $html .= '<span class="filament-tables-badge bg-primary-100 text-primary-800 text-xs px-2 py-1 rounded mr-1">' . e($field) . '</span>';
        }

        if ($remaining > 0) {
            $html .= '<span class="text-xs text-gray-500">' . __('filament-scout-manager::filament-scout-manager.models.fields.more_count', ['count' => $remaining]) . '</span>';
        }

        return $html;
    }
}
