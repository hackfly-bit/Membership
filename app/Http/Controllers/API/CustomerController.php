<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Models\Cabang;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Exports\ReportDetailTransakisCustomer;
use App\Exports\ReportCustomerAll;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use App\Imports\CustomerImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
    }

    public function index()
    {

        // return request();

        $cabangId = request()->cabangId ?? null;
        $cabangFilter = request()->cabangFilter == 'null' ? null : request()->cabangFilter;
        $customers = Customer::useFilters()
            ->whereHas('cabang', function ($query) use ($cabangId, $cabangFilter) {
                if ($cabangId == 1) {
                    if ($cabangFilter !== null) {
                        if ($cabangFilter == 'ALL') {
                            return;
                        }
                        $query->where('nama_cabang', $cabangFilter);
                    }
                } else {
                    $query->where('id', $cabangId);
                }
            })
            ->dynamicPaginate();
        // $customers = Customer::with('transaksi')->useFilters()->dynamicPaginate()->sortByDesc('created_at');

        return CustomerResource::collection($customers);
    }

    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return $this->responseCreated('Customer created successfully', new CustomerResource($customer));
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->responseSuccess(null, new CustomerResource($customer));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return $this->responseSuccess('Customer updated Successfully', new CustomerResource($customer));
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->responseDeleted();
    }

    public function getCustomerData(Request $request)
    {
        $cabangId = $request->cabang_id ?? null;
        $start = $request->from_date ?? "2021-01-01";
        $end = $request->to_date ?? date('Y-m-d');
        $cabang_filter = $request->cabang_filter ?? null;

        $cabangFilter =  Cabang::where('nama_cabang', $cabang_filter)->first();

        if ($request->export == true) {
            if ($request->type === 'pdf') {
                return $this->exportPdfAll($cabangId, $start, $end, $cabangFilter);
            }
            if ($request->type === 'excel') {
                return $this->exportExcelAll($cabangId, $start, $end, $cabangFilter);
            }
        }

        return $this->responseSuccess('Tidak Ada Tipe Parameter ');
    }

    public function exportPdfAll($cabangId, $start, $end, $cabangFilter)
    {

        $customers = Customer::whereHas('cabang', function ($query) use ($cabangId, $cabangFilter) {
            if ($cabangId == 1) {
                if ($cabangFilter->id == 1) {
                   return;
                }else{
                    $query->where('id', $cabangFilter->id);
                }
            } else{
                $query->where('id', $cabangId);
            }
        })
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->get();

        $pdf = PDF::loadView('pdf.customer-export', [
            'customers' => $customers,
        ]);


        $pdf->setPaper('a4', 'landscape');
        $date = date('Y-m-d');

        return $pdf->download('customer-report-' . $date . '.pdf');
    }

    public function exportExcelAll($cabangId, $start, $end, $cabangFilter)
    {

        $customers = Customer::whereHas('cabang', function ($query) use ($cabangId, $cabangFilter) {
            if ($cabangId == 1) {
                if ($cabangFilter->id == 1) {
                   return;
                }else{
                    $query->where('id', $cabangFilter->id);
                }
            } else{
                $query->where('id', $cabangId);
            }
        })
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->get();

        $collctCustomer = [];
        foreach ($customers as $x) {
            $collctCustomer[] = [
                'ID' => $x->id,
                'Nama' => $x->nama,
                'Nomer Hp' => $x->nohp,
                'Email' => $x->email,
                'Role' => $x->role,
                'Cabang' => $x->cabang->nama_cabang,
                'Created At' => $x->created_at,
                'Updated At' => $x->updated_at,
            ];
        }

        $date = date('Y-m-d');
        return Excel::download(new ReportCustomerAll($collctCustomer), 'customer-report-all-' . $date . '.xlsx');
    }



    public function getCustomerReport(Request $request, $id)
    {

        if ($request->export == true) {
            if ($request->type === 'pdf') {
                return $this->exportPdf($id);
            }
            if ($request->type === 'excel') {
                return $this->exportExcel($id);
            }
        }

        $customer = Customer::find($id);
        $transaksi = $customer->transaksi;
        $withdraw = $customer->withdraw;
        $point = ($this->getPoint($transaksi) - $this->getWdPoint($withdraw));
        $nominal_belanja = $this->countPointTransaksi($transaksi);
        return $this->responseSuccess('Customer Report', [
            'customer' => $customer,
            'transaksi' => $transaksi,
            'withdraw' => $withdraw,
            'point' => $point,
            'nominal_belanja' => $nominal_belanja
        ]);
    }

    public function exportPdf($id)
    {
        $customer = Customer::find($id);
        $transaksi = $customer->transaksi;
        $withdraw = $customer->withdraw;

        $pdf = PDF::loadView('pdf.customer-detail-transaksi', [
            'customer' => $customer,
            'transaksi' => $transaksi,
            'withdraw' => $withdraw,
        ]);
        $pdf->setPaper('a4', 'landscape');
        $date = date('Y-m-d');
        return $pdf->download('customer-report-' . $customer->nama . '-' . $date . '.pdf');
    }

    public function exportExcel($id)
    {
        $customer = Customer::find($id);
        $transaksi = $customer->transaksi;
        $withdraw = $customer->withdraw;

        $collctTransaksi = [];
        foreach ($transaksi as $x) {
            $collctTransaksi[] = [
                'ID' => $x->id,
                'Code' => $x->code,
                'Customer' => $x->customer->nama,
                'Tanggal' => $x->tanggal,
                'Nominal' => $x->nominal,
                'Kategori' => $x->kategori->nama,
                'Point' => $x->point,
                'Keterangan' => $x->keterangan,
                'Created At' => $x->created_at,
                'Updated At' => $x->updated_at,
            ];
        }

        $collctWithdraw = [];
        foreach ($withdraw as $x) {
            $collctWithdraw[] = [
                'ID' => $x->id,
                'Nama Customer' => $x->customer->nama,
                'Nomer Hp' => $x->customer->no_hp,
                'Point' => $x->point,
                'Keterangan' => $x->keterangan,
                'Created At' => $x->created_at,
            ];
        }

        // export excel multiple sheet
        return Excel::download(new ReportDetailTransakisCustomer($collctWithdraw, $collctTransaksi), 'customer-report-' . $customer->nama . '.xlsx');
    }


    function countPointTransaksi($transaksi)
    {
        if (empty($transaksi)) {
            return "Belum Bertransaksi";
        }

        $total = 0;
        foreach ($transaksi as $item) {
            $total += $item->nominal;
        }
        return "Rp. " . number_format($total, 0, ',', '.');
    }

    function getPoint($transaksi)
    {
        if (empty($transaksi)) {
            return 0;
        }

        $total = 0;
        foreach ($transaksi as $item) {
            $total += $item->point ?? 0;
        }
        return $total;
    }

    function getWdPoint($withdraw)
    {
        if (empty($withdraw)) {
            return 0;
        }

        $total = 0;
        foreach ($withdraw as $item) {
            $total += $item->point;
        }
        return $total;
    }

    // upload data customer and insert to database

    public function uploadDataCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('file');
        $file_name = time() . $file->getClientOriginalName();
        $file->move('uploads/customer', $file_name);

        $path = public_path('uploads/customer/' . $file_name);

        $success = [];

        try {
            $data =  Excel::toCollection(new CustomerImport, $path);
            foreach ($data as $row) {
                $heading = $row[0];
                for ($i = 1; $i < count($row); $i++) {
                    $data = $row[$i];
                    if (isset($data[0], $data[1], $data[2], $data[3])) {
                        $customer =  Customer::create([
                            'nama' => $data[0],
                            'nohp' => $data[1],
                            'email' => $data[2],
                            'cabang_id' => $data[3],
                            'role' => 'customer',
                        ]);

                        if ($customer) {
                            $success[] = "Customer created successfully: " . $data[0];
                        }
                    } else {
                        Log::error("One or more required columns are missing for row: " . json_encode($data));
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        // clear in server  public_path('uploads/customer/
        unlink($path);

        return response()->json([
            'success' => $success,
            'total' => count($success) . ' Customer created successfully'
        ]);
    }

    public function downloadTemplateCustomer()
    {
        $path = public_path('uploads/template/upload-customer.xlsx');
        return response()->download($path);
    }

    public function checkUniquePhone(Request $request)
    {
        $phone = $request->phone;
        $customer = Customer::where('nohp', $phone)->first();
        if ($customer) {
            return response()->json(['status' => true, 'message' => 'Nomer Hp Sudah Terdaftar', 'data' => $customer->nohp]);
        }
        return response()->json(['status' => false, 'message' => 'Nomer Hp Belum Terdaftar', 'data' => $phone]);
    }

    public function checkingMaxPoint($id, $wdpoint)
    {
        $wdpoint = (int)$wdpoint;
        $customer = Customer::find($id);
        $transaksi = $customer->transaksi;
        $withdraw = $customer->withdraw;
        $point = ($this->getPoint($transaksi) - $this->getWdPoint($withdraw));
        if ($wdpoint > $point) {
            return response()->json(['status' => false, 'message' => 'Point Tidak Cukup']);
        }
        return response()->json(['status' => true, 'message' => 'Point Cukup']);
    }


    public function getIds()
    {
        $cabangId = request()->cabangId ?? null;
        $cabangFilter = request()->cabangFilter == 'null' ? null : request()->cabangFilter;
        $customers = Customer::useFilters()
            ->whereHas('cabang', function ($query) use ($cabangId, $cabangFilter) {
                if ($cabangId == 1) {
                    if ($cabangFilter !== null) {
                        if ($cabangFilter == 'ALL') {
                            return;
                        }
                        $query->where('nama_cabang', $cabangFilter);
                    }
                } else {
                    $query->where('id', $cabangId);
                }
            })
            ->dynamicPaginate();

        return response()->json($customers->pluck('id'));
    }





}
