<?php

namespace App\Console\Commands\Message;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\TCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EveryDayNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Message:EveryDayNews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每日60S读懂世界 定时发送至Q群';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));

        $newsImg=Http::get('https://api.vvhan.com/api/60s')->body();
        $group=QBotDB::getConfig('system','group');
        $qbot->send_group_msg($group,TCode::image('base64://'.base64_encode($newsImg)));
        return 0;
    }
}
