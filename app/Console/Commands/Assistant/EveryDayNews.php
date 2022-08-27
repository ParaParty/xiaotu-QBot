<?php

namespace App\Console\Commands\Assistant;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\TCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class EveryDayNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Assistant:EveryDayNews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '小助手的每日早间新闻';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));
        //检索用户
        $user = DB::table('users')
            ->where('user_data->助手系统->我的助手->启用', true)
            ->where('user_data->助手系统->我的助手->到期时间', '>=', time());

        //新闻速读图片
        $newsImg = Http::get('https://api.vvhan.com/api/60s')->body();

        $user->get()->each(
            static function ($item) use ($qbot, $newsImg) {
                $qbot->send_private_msg($item->user_id, '这是小梦刚才为您整理的今日新闻速读');
                $qbot->send_private_msg($item->user_id, TCode::image('base64://' . base64_encode($newsImg)));
            });
        return 0;
    }
}
