<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Cabang;
use App\Models\Customer;
use App\Models\Kategori;
use App\Models\Withdraw;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Can;

class DashboardController extends Controller
{

    /*
    Summary Transaksi Hari ini 
    Tanggal Hari ini
    Total Transaksi Hari ini
    Total Withdraw Hari ini
    */

    public function __invoke(Request $request)
    {
        $cabang_id = $request->cabang_id;
        $data = [];
        if ($cabang_id == 1) {
            $data['count_customer'] = Customer::count();
            $data['count_transaksi'] = Transaksi::count();
            $data['count_cabang'] = Cabang::count();
            $data['count_kategori'] = Kategori::count();
            $data['summary'] = $this->getSummaryTransaksi($cabang_id);

            // return response()->json(['message' => 'Unauthorized'], 401);
        } else {
            $data['count_customer'] = $this->getCustomer($cabang_id);
            $data['count_transaksi'] = $this->getTransaksi($cabang_id);
            $data['count_cabang'] = 0;
            $data['count_kategori'] = 0;
            $data['summary'] = $this->getSummaryTransaksi($cabang_id);
        }


        return response()->json($data);
    }

    private function getCustomer($cabang_id)
    {
        $customer = Customer::where('cabang_id', $cabang_id)->count();
        return $customer;
    }
    private function getTransaksi($cabang_id)
    {
        $transaksi = Customer::where('cabang_id', $cabang_id)->with('transaksi')->get();
        $count_transaksi = $transaksi->count();
        return $count_transaksi;
    }

    private function getSummaryTransaksi($cabang_id)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        if ($cabang_id == 1) {
            $count_transaksi = Transaksi::where('created_at', $dateNow)->count();
            $count_withdraw = Withdraw::where('created_at', $dateNow)->count();
        } else {
            $transaksi = Transaksi::with('customer')->whereHas('customer', function ($query) use ($cabang_id) {
                $query->where('cabang_id', $cabang_id);
            })->whereDate('created_at', $dateNow)->get();

            $count_transaksi = $transaksi->count();

            $widthdraw = Withdraw::with('customer')->whereHas('customer', function ($query) use ($cabang_id) {
                $query->where('cabang_id', $cabang_id);
            })->whereDate('created_at', $dateNow)->get();

            $count_withdraw = $widthdraw->count();
        }

        // make date format just month and day example 05/02
        $date = Carbon::now()->format('d/m');
        $summary = [
            'tanggal' => $date,
            'transaksi' => $count_transaksi,
            'withdraw' => $count_withdraw
        ];


        return $summary;
    }
}
