<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Template;
use App\Models\Broadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\BroadcastQueue;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerData;
    protected $message;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $customerData, $message, $token)
    {
        $this->customerData = $customerData;
        $this->message = $message;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Retrieve the customer phone and name from customerData
        $phone = $this->customerData['nohp'];
        $name = $this->customerData['nama'];


        $getBroadcastQueue = BroadcastQueue::where('status', false)->get();

        foreach ($getBroadcastQueue as $key => $value) {
            $phone = $value->nomer_hp;
            $message = $value->message;
            $token = $value->token;

            $send = $this->sendMessageV2($phone, $message, $token);
            if ($send) {
                // update status in broadcast queue
                $value->status = true;
                $value->save();

                Log::info('Success Send : ' . $name . ' - ' . $phone);
            } else {
                Log::info('Failed Send : ' . $name . ' - ' . $phone);
            }

            // sleep for 1 second   
            sleep(20);
        }



        // Send the message fixed to the phone number
        // $send = $this->sendMessageV2($phone, $this->message, $this->token);
        // if ($send) {
        //     Log::info('Success: ' . $name . ' - ' . $phone . ' - ' . $this->message);
        // } else {
        //     Log::info('Failed: ' . $name . ' - ' . $phone . ' - ' . $this->message);
        // }
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

        Log::info("Data: " . json_encode($data));

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

        if (isset($response['message_status']) &&  $response['message_status'] == 'Success') {
            return true;
        } else {
            return false;
        }
    }

    protected function sendMessageV2TestMode($phone, $message, $token)
    {
        $curl = curl_init();
        $data = [
            'authkey' => 'br93xbgCWZJv20mGaNXJcS2GCdVTREqb0Sw17BYvujayOJYwkB',
            'appkey' => '51adc0a0-1a46-4577-a5de-977f348ec3ab',
            'to' => $phone,
            'message' => $message,
        ];

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
                'appkey' => '51adc0a0-1a46-4577-a5de-977f348ec3ab',
                'authkey' => 'br93xbgCWZJv20mGaNXJcS2GCdVTREqb0Sw17BYvujayOJYwkB',
                'to' => "6282331431936",
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
