<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BotServices\KremplService;
use App\Exceptions\BotException;
use DOMDocument;
use Carbon\Carbon;
use App\Models\FridgeModel;
use App\Models\BotFridgePage;
use Illuminate\Support\Facades\DB;


class FetchFridgesCommand extends Command
{

    protected $signature = 'fridges:fetch {page?}';
    protected $description = 'Fetch fridges data from the API';

    /**
     * Execute the console command.
     */
    public function handle(KremplService $kremplService)
    {

        DB::beginTransaction();


        try {

            $page = BotFridgePage::where('status', 0)->value('page');

            if ($page !== null) {
                // İlgili kaydın status değerini 1 olarak güncelle
                BotFridgePage::where('page', $page)->update(['status' => 1]);

                $this->info("Page status updated to 1 for page number: " . $page);
            } else {
                $this->info("No pages found with status 0.");
            }
            // Sayfa numarası null ise varsayılan olarak null gönder
            $data = $kremplService->getFridges($page ?: null);

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->validateOnParse = true;
            $dom->loadHTML($data);

            $nav=$dom->getElementsByTagName('nav')->item(0);
            $lis = $nav->getElementsByTagName('li');


            $pages=[];

            foreach ($lis as $li) {
                $aTag = $li->getElementsByTagName('a')->item(0); // İlk <a> etiketini al
                if ($aTag) { // Eğer <a> etiketi varsa
                    $href = $aTag->getAttribute('href'); // href değerini al
                    if (preg_match('/\/(\d+)$/', $href, $matches)) {
                        $pageNumber = $matches[1]; // İlk grup (numara)
                        $pages[] = $pageNumber; // Numara değerini diziye ekle
                    }
                }
            }

            //aynı olanları ayıkla
            $pages = array_unique($pages);

            //olmayan sayfaları db ye statusu 0 olarak ekler

            $existingPages = BotFridgePage::pluck('page')->toArray();
            $pagesToAdd = array_diff($pages, $existingPages);

            foreach ($pagesToAdd as $pageNumber) {
                BotFridgePage::create([
                    'page' => $pageNumber,
                ]);
            }



            $tables = $dom->getElementsByTagName('table')->item(0);
            $trs = $tables->getElementsByTagName('tr');

            $data = [];
            $timestamp = Carbon::now();

            foreach($trs as $tr){
                if ($tr->getElementsByTagName('td')->length) {
                    $model_no = trim(str_replace('No.','',$tr->getElementsByTagName('td')->item(0)->nodeValue));
                    $model = $tr->getElementsByTagName('td')->item(2)->hasAttribute('class') && strpos($tr->getElementsByTagName('td')->item(2)->getAttribute('class'), 'is-empty') ? '' :trim(str_replace('Model','',$tr->getElementsByTagName('td')->item(2)->nodeValue));
                    $production_period = $tr->getElementsByTagName('td')->item(3)->hasAttribute('class') && strpos($tr->getElementsByTagName('td')->item(3)->getAttribute('class'), 'is-empty') ? '' :trim(str_replace('Production period','',$tr->getElementsByTagName('td')->item(3)->nodeValue));


                    $data[] = [
                        'model_no' => $model_no,
                        'model' => $model,
                        'production_period' =>  $production_period,
                    ];
                }
            }


            foreach ($data as $item) {
                $existingModel = FridgeModel::where('model_no', $item['model_no'])->first();

                if (!$existingModel) {
                    // Veritabanında model mevcut değil, yeni bir kayıt ekleyelim
                    FridgeModel::create([
                        'model_no' => $item['model_no'],
                        'model' => $item['model'],
                        'production_period' => $item['production_period'] ? date('Y-m-d', strtotime($item['production_period'])) : null,
                    ]);
                }
            }

            DB::commit();
            $this->info("Fetched fridges data successfully.");


        } catch (BotException $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return 1; // Komut hata kodu döner
        }

        return 0; // Komut başarı kodu döner
    }
}
