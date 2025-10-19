<?php

namespace App\Models;

use App\Filters\PengumumanFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pengumuman extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = PengumumanFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'judul',
		'isi',
    ];


}
