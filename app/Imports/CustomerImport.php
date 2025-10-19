<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;


class CustomerImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row){
            // Check if all required columns exist
            if(isset($row['nama'], $row['nohp'], $row['email'], $row['cabang_id'])) {
               $data =  Customer::create([
                    'nama' => $row['nama'],
                    'nohp' => $row['nohp'],
                    'email' => $row['email'],
                    'code_cabang' => $row['code_cabang'],   
                    'role' => 'customer',
                ]);

                Log::info("Customer created successfully: " . $data->nama);

            } else {
                // Handle error if any required column is missing
                Log::error("One or more required columns are missing for row: " . json_encode($row));
                // You may want to throw an exception here or handle it in some other way
            }
        }
    }
}
