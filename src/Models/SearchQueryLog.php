<?php

namespace MuhammadNawlo\FilamentScoutManager\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQueryLog extends Model
{
    protected $table = 'scout_search_logs';

    protected $fillable = [
        'query',
        'model_type',
        'result_count',
        'execution_time',
        'user_id',
        'ip_address',
        'user_agent',
        'successful',
    ];

    protected $casts = [
        'result_count' => 'integer',
        'execution_time' => 'float',
        'successful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function scopePopular($query, $days = 30)
    {
        return $query->select('query', \DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('query')
            ->orderByDesc('total');
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }
}
