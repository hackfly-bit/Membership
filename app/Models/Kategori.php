<?php

namespace App\Models;

use App\Filters\KategoriFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Kategori extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = KategoriFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'nama_kategory',
		'point',
    ];


}
