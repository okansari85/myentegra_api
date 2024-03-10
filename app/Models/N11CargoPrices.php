<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N11CargoPrices extends Model
{
    use HasFactory;

    protected $table = 'n11_cargo_prices';

    protected $fillable = [
        'desi',
        'yk_price',
        'aras_price',
        'mng_price',
        'ptt_price',
        'sendeo_price',
        'surat_price',
    ];

    protected function kdv_ve_posta_bedeli_ekle ($fiyat){
        $fiyat= $fiyat * 1.20;
        $fiyat = $fiyat * 1.0235;
        return $fiyat;
    }

    protected function getYkPriceAttribute($value)
    {
        // Add VAT (KDV) to YK price
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        $sms_ucreti_dahil = $value + (0.38*1.2);

        return number_format((float)$sms_ucreti_dahil, 2, '.', '');
    }

    protected function getArasPriceAttribute($value)
    {
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        return number_format((float)$value, 2, '.', '');
    }

    protected function getMngPriceAttribute($value)
    {
        // Add VAT (KDV) to YK price
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        return number_format((float)$value, 2, '.', '');
    }

    protected function getPttPriceAttribute($value)
    {
        // Add VAT (KDV) to YK price
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        return number_format((float)$value, 2, '.', '');
    }

    protected function getSuratPriceAttribute($value)
    {
        // Add VAT (KDV) to YK price
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        return number_format((float)$value, 2, '.', '');
    }


    protected function getSendeoPriceAttribute($value)
    {
        // Add VAT (KDV) to YK price
        $value = $this->kdv_ve_posta_bedeli_ekle($value);
        return number_format((float)$value, 2, '.', '');
    }



    

}
