<?php

namespace App\Models;

use App\Filters\TransaksiFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Transaksi extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = TransaksiFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'code',
		'customer_id',
		'tanggal',
		'nominal',
		'kategori_id',
        'point',
		'keterangan',
    ];



	public function setTanggalAttribute($value)
	{
		$this->attributes['tanggal'] = date('Y-m-d', strtotime($value));
	}

	public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(\App\Models\Customer::class);
	}

	public function kategori(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(\App\Models\Kategori::class);
	}

}
