<?php

namespace App\Models;

use App\Filters\CabangFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class BroadcastQueue extends Model
{
    use HasFactory;


    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $table = 'broadcast_queue';


}
