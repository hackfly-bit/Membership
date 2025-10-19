<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BroadcastQueue;
use Illuminate\Support\Facades\Log;


class RunLaravelJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-laravel-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handdling Laravel Job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the customer phone and name from customerData

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

                Log::info('Success Send : ' . $phone);
            } else {
                Log::info('Failed Send : ' . $phone);
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
}
