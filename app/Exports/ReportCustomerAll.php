<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportCustomerAll implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
      return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Nomer HP',
            'Email',
            'Role',
            'Cabang',
            'Created At',
            'Updated At',
        ];
    }
}