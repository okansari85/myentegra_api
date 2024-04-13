<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductImages;
use Illuminate\Support\Facades\Storage;


class DeleteUnusedFiles extends Command
{

    protected $signature = 'delete:unused-files';
    protected $description = 'Command description';

    public function handle()
    {

        echo "Kullanılmayan dosyaları silmeye başlanıyor...\n";

        $using_images=[];
        $images = ProductImages::whereNotNull('product_id')->get();

        foreach ($images as $image) {
            $using_images[] = trim('files/'.$image->name.'.'.$image->type);
        }

        $files = Storage::disk('my_files')->allFiles('files');

        foreach ($files as $file) {
            if (!in_array($file, $using_images)) {
                try {
                    Storage::disk('my_files')->delete($file);
                    echo "Dosya {$file} başarıyla silindi.\n";
                } catch (\Exception $e) {
                    echo "Dosya {$file} silinemedi. Hata: " . $e->getMessage() . "\n";
                }
            }
        }

        //dbden de sil
        ProductImages::whereNull('product_id')->delete();

        echo "Tüm işlemler bitti.\n";


        echo "Kullanılmayan dosyaların silme işlemi tamamlandı.\n";

    }
}
