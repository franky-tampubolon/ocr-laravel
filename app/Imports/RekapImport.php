<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class RekapImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        // $data = [];
        // foreach($collection as $row)
        // {
        //     $data[] = [
        //         'no_btd' => $row[23],
        //         'amount' => $row[10],
        //         'due_date' => $row[8]
        //     ];
        // }
        // return $data;
    }
}
