<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Services\BotServices\IlacFiyatiService;
use App\Exceptions\BotException;
use DOMDocument;
use Carbon\Carbon;
use App\Models\Medicines;
use App\Models\BotMedicinePages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FetchIlacFromIlacFiyat extends Command
{
    protected $signature = 'fetch:ilaclar {page?}';
    protected $description = 'Fetch ilaclar data from the ilacfiyat.com';


    public function handle(IlacFiyatiService $ilacFiyatService)
    {
        //

        DB::beginTransaction();


        try {


            $page = BotMedicinePages::where('status', 0)->value('page');

            if ($page !== null) {
                // İlgili kaydın status değerini 1 olarak güncelle
                BotMedicinePages::where('page', $page)->update(['status' => 1]);

                $this->info("Page status updated to 1 for page number: " . $page);
            } else {
                $this->info("No pages found with status 0.");
            }
            // Sayfa numarası null ise varsayılan olarak null gönder
            $data = $ilacFiyatService->getMedicines($page ?: null);

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->validateOnParse = true;
            $dom->loadHTML($data);


            $nav=$dom->getElementsByTagName('nav')->item(1);
            $lis = $nav->getElementsByTagName('li');

            $pages=[];


            foreach ($lis as $li) {
                $aTag = $li->getElementsByTagName('a')->item(0); // İlk <a> etiketini al
                if ($aTag) { // Eğer <a> etiketi varsa
                    // aria-label="İleri" veya aria-label="Sonuncu" içeren linkleri atla
                    $ariaLabel = $aTag->getAttribute('aria-label');
                    if ($ariaLabel === 'İleri' || $ariaLabel === 'Sonuncu') {
                        continue;
                    }

                    $href = $aTag->getAttribute('href'); // href değerini al
                    if (preg_match('/pg=(\d+)/', $href, $matches)) {
                        $pageNumber = (int) $matches[1]; // Sayı değerini al ve integer'a çevir
                        $pages[] = $pageNumber; // Sayfa numarasını diziye ekle
                    }
                }
            }


            //aynı olanları ayıkla
            $pages = array_unique($pages);


            $existingPages = BotMedicinePages::pluck('page')->toArray();
            $pagesToAdd = array_diff($pages, $existingPages);

            foreach ($pagesToAdd as $pageNumber) {
                BotMedicinePages::create([
                    'page' => $pageNumber,
                ]);
            }



            $medicines = [];

            // Tüm 'medicine-item' öğelerini al
            $xpath = new \DOMXPath($dom);
            $medicineItems = $xpath->query('//div[contains(@class, "medicine-item")]');


            foreach ($medicineItems as $medicine) {
                // İlaç bilgilerini almak
                $name = $xpath->query('.//a', $medicine)->item(0)->nodeValue;
                $barcode = $xpath->query('.//div[contains(@class, "item-content")]', $medicine)->item(0)->nodeValue;
                $activeIngredient = $xpath->query('.//div[contains(@class, "item-content")]', $medicine)->item(1)->nodeValue;
                $importType = $xpath->query('.//div[contains(@class, "item-content")]', $medicine)->item(2)->nodeValue;
                $status = $xpath->query('.//div[contains(@class, "item-content medicine-status-active")]', $medicine)->item(0)->nodeValue;
                $price = $xpath->query('.//div[contains(@class, "fw-bold font-size-18")]', $medicine)->item(0)->nodeValue;

                // İlaç resmini al
                $image = $xpath->query('.//img', $medicine)->item(0);
                $imageUrl = $image ? $image->getAttribute('src') : null;

                // Resmin URL'si geçerli değilse devam et
                if ($imageUrl) {
                    // Resmin dosya adı
                    $imageName = basename($imageUrl);  // URL'den dosya adı çıkarılır

                    // Resmi indirme ve kaydetme
                    $imagePath = Storage::disk('public')->put('medicines/' . $imageName, file_get_contents('https://ilacfiyati.com'.'/'.$imageUrl));

                    // İlaç bilgilerini ve resim yolunu diziye ekliyoruz
                    $medicines[] = [
                        'name' => trim($name),
                        'barcode' => trim($barcode),
                        'active_ingredient' => trim($activeIngredient),
                        'import_type' => trim($importType),
                        'status' => trim($status),
                        'price' => trim($price),
                        'image_url' => url('storage/medicines/' . $imageName), // Resim URL'si
                    ];
                }
            }


            foreach ($medicines as $data) {
                $price = floatval(str_replace(',', '.', str_replace(' TL', '', $data['price']))); // TL'yi kaldır ve ondalık ayracı düzelt

                Medicines::updateOrCreate(
                    ['barcode' => $data['barcode']], // Eğer barkod varsa güncelle
                    [
                        'name' => $data['name'],
                        'active_ingredient' => $data['active_ingredient'],
                        'import_type' => $data['import_type'],
                        'status' => $data['status'],
                        'price' => $price,
                        'image_url' => $data['image_url'],
                    ]
                );
            }



            DB::commit();
            $this->info("Fetched medicines data successfully.");


        } catch (BotException $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1; // Komut hata kodu döner
        }

        return 0; // Komut başarı kodu döner
    }
}
