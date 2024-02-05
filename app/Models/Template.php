<?php

namespace App\Models;

use App\Filters\TemplateFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Template extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = TemplateFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'judul',
		'message',
    ];


}
