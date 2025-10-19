<?php

namespace App\Filters;

use Essa\APIToolKit\Traits\DateFilter;
use Essa\APIToolKit\Filters\QueryFilters;


class TransaksiFilters extends QueryFilters
{
    // use DateFilter;
    // protected string $dateColumnName = 'tanggal';
    // protected array $allowedSorts = ['tanggal'];
    // protected array $allowedFilters = ['cabang_id'];
    // protected array $allowedIncludes = ['customer'];

    protected array $columnSearch = ['code',];

    protected array $relationSearch = [
        'customer' => ['nama'],
        'kategori' => ['nama_kategory'],
    ];
}
