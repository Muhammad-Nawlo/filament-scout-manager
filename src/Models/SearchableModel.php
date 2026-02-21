<?php

namespace MuhammadNawlo\FilamentScoutManager\Models;

use Illuminate\Database\Eloquent\Model;

class SearchableModel extends Model
{
    protected $table = 'searchable_models';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
