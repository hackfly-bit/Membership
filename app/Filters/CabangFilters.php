<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CabangFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ['nama_cabang'];
}
