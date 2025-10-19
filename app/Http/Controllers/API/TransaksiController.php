<?php

namespace App\Http\Controllers\API;

use PharIo\Manifest\Url;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;
use App\Models\Kategori;
use App\Models\Cabang;
use App\Models\Broadcast;
use App\Models\Withdraw;
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
use App\Http\Controllers\API\BroadcastController;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;


class TransaksiController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
    }

    public function index()
    {
        // $date_from = date('Y-m-d', strtotime(request()->from_date));
        // $date_to = date('Y-m-d', strtotime(request()->to_date));
        $cabangId = request()->cabangId ?? null;
        $cabangFilter = request()->cabang_filter == 'null' ? null : request()->cabang_filter;

        // return request()->all();
        $transaksis = Transaksi::useFilters()
            ->whereHas('customer', function ($query) use ($cabangId, $cabangFilter) {

                if ($cabangId == 1) {
                    if ($cabangFilter !== null) {
                        if ($cabangFilter == 'ALL') {
                            return;
                        }

                        $getCabangId = Cabang::where('nama_cabang', $cabangFilter)->first();
                        $query->where('cabang_id', $getCabangId->id);
                    }
                } else {
                    $query->where('cabang_id', $cabangId);
                }
            });

        // if isset date_from and date_to
        if (request()->from_date && request()->to_date) {
            $transaksis = $transaksis->whereBetween('tanggal', [request()->from_date, request()->to_date]);
        }


        // sorting data by date 
        $transaksis = $transaksis->orderBy('created_at', 'desc');


        return  TransaksiResource::collection($transaksis->dynamicPaginate());
    }

    public function store(CreateTransaksiRequest $request)
    {

        // $request->validated();


        // return $request->all();
        $kategori = Kategori::find($request->kategori_id);
        $nominal = preg_replace('/\D/', '', $request->nominal);

        $transaksi = Transaksi::create([
            'code' => $request->code,
            'customer_id' => $request->customer_id,
            'tanggal' => $request->tanggal,
            // Rp. 100,000 replace to int
            'nominal' => $nominal,
            'kategori_id' => $request->kategori_id,
            'point' => floor($nominal / $kategori->point),
            'keterangan' => $request->keterangan,

        ]);

        if ($transaksi) {
            if (Auth::user()->token_wa != null) {

                $phone = $transaksi->customer->nohp;
                $setting = Template::where('id', 20)->first();
                $message = $setting->id;
                $token = Auth::user()->token_wa;

                $sendMessage  = (new BroadcastController)->sendMessageAction($request->customer_id,$phone, $message, $token);


                if ($sendMessage) {
                   Log::info('Message sent to ' . $phone);
                } else {
                    Log::info('Message failed to send to ' . $phone);
                }
            }
        }
        return $this->responseCreated('Transaksi created successfully', new TransaksiResource($transaksi));
    }

    public function show(Transaksi $transaksi): JsonResponse
    {
        return $this->responseSuccess(null, new TransaksiResource($transaksi));
    }

    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi): JsonResponse
    {
        $request->validated();
        $kategori = Kategori::find($request->kategori_id);
        $nominal = preg_replace('/\D/', '', $request->nominal);



        $transaksi->update([
            'code' => $request->code,
            'customer_id' => $request->customer_id,
            'tanggal' => $request->tanggal,
            'nominal' => $nominal,
            'kategori_id' => $request->kategori_id,
            'point' => floor($nominal / $kategori->point),
            'keterangan' => $request->keterangan,
        ]);

        return $this->responseSuccess('Transaksi updated Successfully', new TransaksiResource($transaksi));
    }

    public function destroy(Transaksi $transaksi): JsonResponse
    {
        $transaksi->delete();

        return $this->responseDeleted();
    }

    public function ExportExcel()
    {

        $start = date('Y-m-d', strtotime(request()->from_date));
        $end = date('Y-m-d', strtotime(request()->to_date));
        $cabang_id = request()->cabang_id;
        $cabang = request()->cabang_filter == 'null' ? null : request()->cabang_filter;

        $transaksis = Transaksi::useFilters()
            ->whereHas('customer', function ($query) use ($cabang_id, $cabang) {

                if ($cabang_id == 1) {
                    if ($cabang !== null) {
                        if ($cabang == 'ALL') {
                            return;
                        }

                        $getcabang = Cabang::where('nama_cabang', $cabang)->first();

                        $query->where('cabang_id', $getcabang->id);
                    }
                } else {
                    $query->where('cabang_id', $cabang_id);
                }
            })->whereBetween('tanggal', [$start, $end])->get();

        $collection = [];
        foreach ($transaksis as $x) {
            $collection[] = [
                'ID' => $x->id,
                'Code' => $x->code,
                'Customer' => $x->customer->nama,
                'Tanggal' => $x->tanggal,
                'Nominal' => $x->nominal,
                'Kategori' => $x->kategori->nama_kategory,
                'Point' => $x->point,
                'Keterangan' => $x->keterangan,
                'Created At' => $x->created_at,
                'Updated At' => $x->updated_at,
            ];
        }


        if ($transaksis->count() > 0) {
            $file_name = 'transaksi_' . date('Y-m-d') . '.xlsx';
            return Excel::download(new TransaksiExport($collection), $file_name);
        }

        return $this->responseNotFound('Data not found');
    }

    public function ExportPDF()
    {

        $start = date('Y-m-d', strtotime(request()->from_date));
        $end = date('Y-m-d', strtotime(request()->to_date));
        $cabang_id = request()->cabang_id;
        $cabang = request()->cabang_filter == 'null' ? null : request()->cabang_filter;

        $transaksis = Transaksi::useFilters()
            ->whereHas('customer', function ($query) use ($cabang_id, $cabang) {

                if ($cabang_id == 1) {
                    if ($cabang !== null) {
                        if ($cabang == 'ALL') {
                            return;
                        }

                        $getcabang = Cabang::where('nama_cabang', $cabang)->first();

                        $query->where('cabang_id', $getcabang->id);
                    }
                } else {
                    $query->where('cabang_id', $cabang_id);
                }
            })->whereBetween('tanggal', [$start, $end])->get();



        $data =  TransaksiResource::collection($transaksis);
        if ($transaksis->count() > 0) {
            $x = collect($data);
            $pdf = Pdf::loadView('pdf.transaksi', ['transaksis' => $x]);
            $pdf->setPaper('legal', 'lanscape');
            $pdf->save(storage_path('app/transaksi.pdf'));
        }
        $file_path = storage_path('app/transaksi.pdf');
        $date = date('Y-m-d');
        return response()->download($file_path, 'transaksi_' . $date . '_.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }


    public function downloadExcel()
    {
        $file_path = storage_path('app/transaksi.xlsx');
        return response()->download($file_path, 'transaksi.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadPDF()
    {
        $file_path = storage_path('app/transaksi.pdf');
        return response()->download($file_path, 'transaksi.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function getTransaksiByCustomer($id): AnonymousResourceCollection
    {
        $transaksis = Transaksi::where('customer_id', $id)->get();
        return  TransaksiResource::collection($transaksis);
    }

    public function getLastTransaksiByCustomer($id)
    {
        $transaksi = Transaksi::where('customer_id', $id)->latest()->first();
        // sum all point
        $total_point = Transaksi::where('customer_id', $id)->sum('point');
        $toal_wd = Withdraw::where('customer_id', $id)->sum('point');

        $sisa_point = $total_point - $toal_wd;

        // $text = "Terima kasih telah belanja di toko kami, informasi point pada transaksi terakhir anda sebesar " . $transaksi->point . " point, total point anda sebesar " . $sisa_point . " point";

        $template = "Hi " . $transaksi->customer->nama . ",\n\n";
        $template .= "Kami ingin memberitahu Anda tentang transaksi yang telah dilakukan di " . $transaksi->customer->cabang->nama_cabang . " dengan rincian sebagai berikut:\n\n";
        $template .= "Tanggal: " . $transaksi->tanggal . "\n";
        $template .= "Nominal: Rp. " . $transaksi->nominal . "\n";
        $template .= "Point: " . $transaksi->point . "\n";
        $template .= "Kategori: " . $transaksi->kategori->nama_kategory . "\n\n";
        $template .= "Total Point saat ini: " . $sisa_point . "\n\n";
        $template .= "Terima kasih telah mempercayakan " . $transaksi->customer->cabang->nama_cabang . " kepada " . $transaksi->customer->nama . " dan keluarga. Jika terdapat kesalahan dalam informasi pesan ini, mohon segera sampaikan kepada kami.\n\n";
        $template .= "Salam hormat,\n\n";
        $template .= "Management " . $transaksi->customer->cabang->nama_cabang;

        return $template;
    }



    private function sendMessage($phone, $message, $token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone,
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: $token"
            )
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        if ($response['status'] == false) {
            Log::info($response);
            Broadcast::create([
                'api_status' => 'failed',
                'target' => $phone,
                'detail' => $message,
                'process' => 'error send message',
                'status' => 'failed',
                'reason' => $response['reason'],
            ]);
        } else {
            Log::info($response);
            // {"detail":"success! message in queue","id":["31391836"],"process":"processing","status":true,"target":["6282335601637"]}. save to database

            Broadcast::create([
                'api_status' => 'success',
                'target' => isset($response['target'][0]) ? $response['target'][0] : null,
                'detail' => $response['detail'],
                'process' => $response['process'],
                'status' => $response['status'],
                'reason' => 'success',
            ]);
        }
    }
}
