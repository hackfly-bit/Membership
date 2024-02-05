<?php

namespace App\Models;

use App\Filters\BroadcastFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Broadcast extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = BroadcastFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'api_status',
		'detail',
		'process',
		'status',
		'target',
		'reason',
    ];


}
