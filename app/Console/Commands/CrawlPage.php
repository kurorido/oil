<?php

namespace App\Console\Commands;

use App\Models\Price;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class CrawlPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:crawl_page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $newYork = file_get_contents('http://www.stockq.org/commodity/FUTRWOIL.php');
        $newYork = file_get_contents(base_path('index.html'));

        $crawler = new Crawler($newYork);

        $topMap = [
            0 => 'price',
            2 => 'percentage',
            7 => 'time'
        ];

        $topData = [];

        $topTable = $crawler
            ->filter('.indexpagetable tr')
            ->eq(1)
            ->filter('td')
            ->each(function ($node, $i) use (&$topData, $topMap) {
                $key = data_get($topMap, $i);
                if ($key) {
                    $topData[$key] = trim($node->text(), '%');
                }
            });

        $bottomMap = [
            0 => 'date',
            1 => 'price',
            2 => 'percentage'
        ];

        $bottomData = [];

        $bottomTable = $crawler->filter('.indexpagetable')
            ->eq(2)
            ->filter('tr')
            ->eq(1)
            ->filter('td')
            ->each(function ($node, $i) use (&$bottomData, $bottomMap) {
                $key = data_get($bottomMap, $i);
                if ($key) {
                    $bottomData[$key] = trim($node->text(), '%');
                }
            });

        $topData['date'] = $bottomData['date'];
        $bottomData['time'] = $topData['time'];

        Price::create(array_merge($topData, [
            'type' => 'top'
        ]));

        Price::create(array_merge($bottomData, [
            'type' => 'bottom'
        ]));
    }
}
