<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use App\Models\Template;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Broadcast\BroadcastResource;
use App\Http\Requests\Broadcast\CreateBroadcastRequest;
use App\Http\Requests\Broadcast\UpdateBroadcastRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Jobs\SendMessageJob;
use Carbon\Carbon;
use App\Models\BroadcastQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;


class BroadcastController extends Controller
{
    use ApiResponse;
    public function __construct() {}

    public function index(): AnonymousResourceCollection
    {
        $broadcasts = Broadcast::useFilters()->dynamicPaginate();

        return BroadcastResource::collection($broadcasts);
    }

    public function store(CreateBroadcastRequest $request): JsonResponse
    {
        $broadcast = Broadcast::create($request->validated());

        return $this->responseCreated('Broadcast created successfully', new BroadcastResource($broadcast));
    }

    public function show(Broadcast $broadcast): JsonResponse
    {
        return $this->responseSuccess(null, new BroadcastResource($broadcast));
    }

    public function update(UpdateBroadcastRequest $request, Broadcast $broadcast): JsonResponse
    {
        $broadcast->update($request->validated());

        return $this->responseSuccess('Broadcast updated Successfully', new BroadcastResource($broadcast));
    }

    public function destroy(Broadcast $broadcast): JsonResponse
    {
        $broadcast->delete();

        return $this->responseDeleted();
    }

    public function sendSingleMessage(Request $request){
        // request is $customer_id, $phone, $template_id,
        // return $request->all();
        $customer_id = $request->customer_id;
        $template_id = $request->template_id;
        $customer = Customer::find($customer_id);
        $template = Template::find($template_id);
        $phone = $customer->nohp;

        $message = $this->replaceVariables($template->message, $customer);

        if (substr($phone, 0, 2) == '08') {
            $phone = '62' . substr($phone, 1);
        }

        $user = User::find(Auth::id())->first();
        $token = $user->token_wa;

        $broadcastQueue = new BroadcastQueue();
        $broadcastQueue->nomer_hp = $phone;
        $broadcastQueue->message = $message;
        $broadcastQueue->token = $token;
        $broadcastQueue->status = false;
        $broadcastQueue->tanggal_aksi = date('Y-m-d');

             
        try {
            $sendTriger = $this->sendMessageV2($phone, $message, $token);
            if ($sendTriger) {
                $broadcastQueue->status = true;
                $broadcastQueue->save();
            }
        } catch (\Exception $e) {
            Log::info([
                'message' => 'Message failed to send',
                'error' => $e->getMessage()
            ]);
            return false;
        }

        return true;
    }


    public function sendMessageAction($customer_id, $phone, $template_id, $token)
    {
        $template = Template::find($template_id);
        $customer = Customer::find($customer_id);
        $message = $this->replaceVariables($template->message, $customer);

        if (substr($phone, 0, 2) == '08') {
            $phone = '62' . substr($phone, 1);
        }

        // get token_wa from user
        $user = User::find(Auth::id())->first();
        $token = $user->token_wa;

        $broadcastQueue = new BroadcastQueue();
        $broadcastQueue->nomer_hp = $phone;
        $broadcastQueue->message = $message;
        $broadcastQueue->token = $token;
        $broadcastQueue->status = false;
        $broadcastQueue->tanggal_aksi = date('Y-m-d');

       
        try {
            $sendTriger = $this->sendMessageV2($phone, $message, $token);
            if ($sendTriger) {
                $broadcastQueue->status = true;
                $broadcastQueue->save();
            }
        } catch (\Exception $e) {
            Log::info([
                'message' => 'Message failed to send',
                'error' => $e->getMessage()
            ]);
            return false;
        }

        return true;
    }

    public function broadcastAction(Request $request)
    {
        $customers = $request->ids;
        $template = Template::find($request->template_id);
  

        $success = [];
        $failed = [];

        foreach ($customers as $customerId) {
            Log::info("Processing customer ID: " . $customerId);
            $customer = Customer::find($customerId);

            if (!$customer) {
                Log::warning("Customer ID not found: " . $customerId);
                $failed[] = $customerId;
                continue;
            }

            $message = $this->replaceVariables($template->message, $customer);
            $customerData = [
                'nohp' => $customer->nohp,
                'nama' => $customer->nama,
            ];

            // Dispatch the job to the queue with a 60-second delay
            // SendMessageJob::dispatch($customerData, $message, $token)
            //     // ->delay(Carbon::now()->addSeconds(60))
            //     ->onQueue('messages');

            // change nohp format from 08xxx to 628xxx
            if (substr($customer->nohp, 0, 2) == '08') {
                $customer->nohp = '62' . substr($customer->nohp, 1);
            }

            // get token_wa from user
            $user = User::find(Auth::id())->first();
            $token = $user->token_wa;

            $broadcastQueue = new BroadcastQueue();
            $broadcastQueue->nomer_hp = $customer->nohp;
            $broadcastQueue->message = $message;
            $broadcastQueue->token = $token;
            $broadcastQueue->status = false;
            $broadcastQueue->tanggal_aksi = date('Y-m-d');

            if (!$broadcastQueue->save()) {
                Log::info("Failed to save broadcast queue");
                $failed[] = $customer->nama;
                continue;
            }

            // $this->sendMessageV2($customer->nohp, $message, $token);
            $success[] = $customer->nama; // Note: success here only indicates the job was dispatched
        }

        if(count($success) > 0){
            SendMessageJob::dispatch($customerData, $message, $token);
        }



        return response()->json([
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    private function replaceVariables($message, $customer)
    {
        // Nominal blanja
        // Tanggal belanja
        // Kategori
        // Keterangan belanja
        $variables = [
            '{{customer.nama}}' => $customer->nama,
            '{{customer.phone}}' => $customer->nohp,
            '{{customer.email}}' => $customer->email,
            '{{customer.cabang}}' => $customer->cabang->nama_cabang ?? null,
            '{{customer.cabang.alamat}}' => $customer->cabang->alamat ?? null,
            '{{customer.point}}' => ($this->getPoint($customer->transaksi) - $this->getWdPoint($customer->withdraw)),
            // customer last transaction 
            '{{customer.last.trx}}' => $customer->transaksi->last()->code ?? null,
            '{{customer.last.nominal}}' => $customer->transaksi->last()->nominal ?? null,
            '{{customer.last.tanggal}}' => $customer->transaksi->last()->tanggal ?? null,
            '{{customer.last.kategori}}' => $customer->transaksi->last()->kategori->nama_kategory ?? null,
            '{{customer.last.keterangan}}' => $customer->transaksi->last()->keterangan ?? null,
            // customer last withdraw
            '{{customer.last.withdraw}}' => $customer->withdraw->last()->point ?? null,
            '{{customer.last.withdraw.tanggal}}' => $customer->withdraw->last()->created_at ?? null,
            '{{customer.last.withdraw.reason}}' => $customer->withdraw->last()->wd_reason ?? null,

            // break line
            '{{br}}' => "\n",

        ];

        return str_replace(array_keys($variables), array_values($variables), $message);
    }

    private function countPointTransaksi($transaksi)
    {
        if (empty($transaksi)) {
            return 0;
        }

        $total = 0;
        foreach ($transaksi as $item) {
            $total += $item->point;
        }
        return $total;
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

    protected function sendMessageV2($phone, $message, $token)
    {
        $curl = curl_init();
        $data = [
            'authkey' => 'br93xbgCWZJv20mGaNXJcS2GCdVTREqb0Sw17BYvujayOJYwkB',
            'appkey' => $token,
            'to' => $phone,
            'message' => $message,
        ];

        // Log::info("Data: " . json_encode($data));

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.wapanels.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => $data['appkey'],
                'authkey' => $data['authkey'],
                'to' => $data['to'],
                'message' => $message,
                'sandbox' => 'false'
            ),
        ));


        $response = curl_exec($curl);
        // $error = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response, true);
        Log::info("Response: " . json_encode($response));

        if ($response['message_status'] == 'Success') {
            return true;
        } else {
            return false;
        }
    }

    protected function sendMessageV1($phone, $message, $token)
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
                'delay' => '1000',
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
