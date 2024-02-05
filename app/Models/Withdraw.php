<?php

namespace App\Models;

use App\Filters\WithdrawFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Withdraw extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = WithdrawFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
		'point',
		'wd_reason',
    ];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }


}
