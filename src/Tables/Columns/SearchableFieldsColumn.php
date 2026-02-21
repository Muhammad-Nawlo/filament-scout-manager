<?php

namespace MuhammadNawlo\FilamentScoutManager\Tables\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Support\Facades\Schema;

class SearchableFieldsColumn extends Column
{
    protected string $view = 'filament-scout-manager::tables.columns.searchable-fields';

    public function getState()
    {
        $record = $this->getRecord();
        $modelClass = $record->class ?? $record;

        try {
            $model = new $modelClass;
            if (method_exists($model, 'toSearchableArray')) {
                $example = $model->toSearchableArray();
                return array_keys($example);
            }

            if (method_exists($model, 'getFillable') && !empty($model->getFillable())) {
                return $model->getFillable();
            }

            $table = $model->getTable();
            return Schema::getColumnListing($table);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getFormattedState()
    {
        $fields = $this->getState();

        if (empty($fields)) {
            return 'No fields configured';
        }

        $displayFields = array_slice($fields, 0, 3);
        $remaining = count($fields) - 3;

        $html = '';
        foreach ($displayFields as $field) {
            $html .= '<span class="filament-tables-badge bg-primary-100 text-primary-800 text-xs px-2 py-1 rounded mr-1">' . e($field) . '</span>';
        }

        if ($remaining > 0) {
            $html .= '<span class="text-xs text-gray-500">+' . $remaining . ' more</span>';
        }

        return $html;
    }
}
