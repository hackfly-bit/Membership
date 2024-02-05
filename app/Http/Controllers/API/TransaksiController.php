<?php

namespace App\Http\Controllers\API;

use PharIo\Manifest\Url;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TransaksiExport;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Transaksi\TransaksiResource;
use App\Http\Requests\Transaksi\CreateTransaksiRequest;
use App\Http\Requests\Transaksi\UpdateTransaksiRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransaksiController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
    
    }

    public function index()
    {
        $cabangId = request()->cabang_id ?? null;
        $transaksis = Transaksi::useFilters()
            ->whereHas('customer', function ($query) use ($cabangId) {
                // if cabang_id is 1, return all data
                if ($cabangId == 1) return;
                $query->where('cabang_id', $cabangId);
            })
            ->dynamicPaginate();

        return  TransaksiResource::collection($transaksis);
    }

    public function store(CreateTransaksiRequest $request): JsonResponse
    {
        $transaksi = Transaksi::create($request->validated());

        return $this->responseCreated('Transaksi created successfully', new TransaksiResource($transaksi));
    }

    public function show(Transaksi $transaksi): JsonResponse
    {
        return $this->responseSuccess(null, new TransaksiResource($transaksi));
    }

    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi): JsonResponse
    {
        $transaksi->update($request->validated());

        return $this->responseSuccess('Transaksi updated Successfully', new TransaksiResource($transaksi));
    }

    public function destroy(Transaksi $transaksi): JsonResponse
    {
        $transaksi->delete();

        return $this->responseDeleted();
    }

    public function ExportExcel()
    {
        $transaksis = Transaksi::useFilters()->dynamicPaginate();
        if ($transaksis->count() > 0) {
            $file =  Excel::store(new TransaksiExport(collect($transaksis)->get('data')), 'transaksi.xlsx');
        }
        $file_path = storage_path('app/transaksi.xlsx');
        return response()->download($file_path, 'transaksi.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function ExportPDF()
    {
        $transaksis = Transaksi::useFilters()->dynamicPaginate();
        $data =  TransaksiResource::collection($transaksis);
        if ($transaksis->count() > 0) {
            $x = collect($data);
            $pdf = Pdf::loadView('pdf.transaksi', ['transaksis' => $x]);
            $pdf->setPaper('A4', 'landscape');
            $pdf->save(storage_path('app/transaksi.pdf'));
        }
        $file_path = storage_path('app/transaksi.pdf');
        return $file_path;
        return response()->download($file_path, 'transaksi.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function getTransaksiByCustomer($id): AnonymousResourceCollection
    {
        $transaksis = Transaksi::where('customer_id', $id)->get();
        return  TransaksiResource::collection($transaksis);
    }
}
