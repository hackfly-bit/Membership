<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransaksiExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Convert data to a collection
        $collection = collect($this->data);

        // Calculate the sum of the 'Nominal' column
        $sum = $collection->sum('Nominal');
        $sumPoint = $collection->sum('Point');

        // Append the sum as the last row
        $collection->push([
            '', '', '', '', $sum, '', $sumPoint, '', '', ''
        ]);

        return $collection;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Customer',
            'Tanggal',
            'Nominal',
            'Kategori',
            'Point',
            'Keterangan',
            'Created At',
            'Updated At',
        ];
    }

    public function title(): string
    {
        return 'History Transaksi';
    }
}