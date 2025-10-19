<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class ReportDetailTransakisCustomer implements WithMultipleSheets
{
    protected $collctWithdraw;
    protected $collctTransaksi;

    public function __construct($collctWithdraw, $collctTransaksi)
    {
        $this->collctWithdraw = $collctWithdraw;
        $this->collctTransaksi = $collctTransaksi;
    }

    // /**
    // * @return array
    // */
    // public function collection()
    // {
    //     return [];
    // }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets  = [
            'Withdraw' => new WithdrawExport($this->collctWithdraw, $this->collctTransaksi),
            'Transaksi' => new TransaksiExport($this->collctTransaksi)
        ];
        return $sheets;
    }

  
}