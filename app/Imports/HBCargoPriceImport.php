<?php

namespace App\Imports;

use App\Models\HBCargoPrices;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;



class HBCargoPriceImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        //dd($row);

        return new HBCargoPrices([
            //
            'desi'     => $row['desi'],
            'aras_price'    => floatval(preg_replace('/[^\d\.]/', '',  str_replace(",",".",$row['aras']))),
            'mng_price'    => floatval(preg_replace('/[^\d\.]/', '',  str_replace(",",".",$row['mng']))),
            'yk_price'    => floatval(preg_replace('/[^\d\.]/', '',  str_replace(",",".",$row['yurtici']))),
            'surat_price'    => floatval(preg_replace('/[^\d\.]/', '',  str_replace(",",".",$row['surat']))),
            'ptt_price'    => floatval(preg_replace('/[^\d\.]/', '',  str_replace(",",".",$row['ptt']))),

        ]);
    }


}
