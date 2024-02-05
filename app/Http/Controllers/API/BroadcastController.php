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

class BroadcastController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
    }

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

    public function broadcastAction(Request $request)
    {
        $customers = $request->ids;
        // return $customers;
        $template = Template::find($request->template_id);
        $token = $request->token_wa;

        // return response()->json([
        //     'customers' => $customers,
        //     'template' => $template,
        // ]);

        $success  = [];
        $failed = [];

        foreach ($customers as $customerId) {
            // logging customer id  
            Log::info($customerId);
            $customer = Customer::find($customerId);
            $message = $this->replaceVariables($template->message, $customer);

            // $this->sendMessage($customer->phone, $message); log if success and failed
            // $success = True;
            // $this->sendMessage($customer->nohp, $message)
            $send = $this->sendMessage($customer->nohp, $message);
            if($send){
                Log::info('success'. $customer->nama. ' : '. $customer->nohp. ' : '. $message);
                $success[] = $customer->nama;
            }else{
                Log::info('failed'. $customer->nama. ' : '. $customer->nohp. ' : '. $message);
                $failed[] = $customer->nama;
            }
        }
        return response()->json([
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    private function replaceVariables($message, $customer)
    {
        $variables = [
            '{{customer.nama}}' => $customer->nama,
            '{{customer.phone}}' => $customer->nohp,
            '{{customer.email}}' => $customer->email,
            '{{customer.cabang}}' => $customer->cabang->alamat ?? null,
            '{{customer.point}}' => $this->countPointTransaksi($customer->transaksi) ?? 0,
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
            $total += $item->kategori->point;
        }
        return $total;
    }



    private function sendMessage($phone, $message)
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