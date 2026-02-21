<?php

namespace MuhammadNawlo\FilamentScoutManager\Models;

use Illuminate\Database\Eloquent\Model;

class Synonym extends Model
{
    protected $table = 'scout_synonyms';

    protected $fillable = [
        'model_type',
        'word',
        'synonyms',
        'engine_settings',
    ];

    protected $casts = [
        'synonyms' => 'array',
        'engine_settings' => 'array',
    ];

    public function getSynonymListAttribute()
    {
        return implode(', ', $this->synonyms ?? []);
    }

    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('model_type', $modelClass);
    }

    public function toMeilisearchFormat()
    {
        return [
            $this->word => $this->synonyms,
        ];
    }

    public function toAlgoliaFormat()
    {
        return [
            'objectID' => $this->word,
            'type' => 'synonym',
            'synonyms' => array_merge([$this->word], $this->synonyms),
        ];
    }
}
