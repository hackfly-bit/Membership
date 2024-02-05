<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CustomerFilters extends QueryFilters
{
    // protected array $allowedFilters = ['cabang_id'];

    protected array $columnSearch = ['nama'];
}
