<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class WithdrawExport implements FromCollection,WithHeadings, withTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $data;
    protected $collctTransaksi;

    public function __construct($data, $collctTransaksi)
    {
        $this->data = $data;
        $this->collctTransaksi = $collctTransaksi;
    }


    public function collection()
    {

        // Convert data to a collection
        $collection = collect($this->data);
        $totalTrans = collect($this->collctTransaksi);

        // Calculate the sum of the 'Nominal' column
        $sum = $collection->sum('Point');
        $sumTrans = $totalTrans->sum('Point');
        $sisa = 'Sisa Point '.$sumTrans - $sum . ' Point';


        // Append the sum as the last row
        $collection->push([
            '','', '', $sum, '', ''
        ]);
        $collection->push([
            '','', '', $sisa, '', ''
        ]);

        return $collection;
        
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Customer',
            'Nomer Hp',
            'Point',
            'Keterangan',
            'Created At',
        ];
    }

    public function title(): string
    {
        return 'History Withdraw';
    }
}
