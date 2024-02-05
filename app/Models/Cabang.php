<?php

namespace App\Models;

use App\Filters\CabangFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cabang extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = CabangFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'nama_cabang',
		'code_cabang',
		'alamat',
		'nohp',
    ];


}
