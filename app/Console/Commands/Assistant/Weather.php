<?php

namespace App\Console\Commands\Assistant;

use App\Class\Api\Tianxing\Tianxing;
use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\TCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use JsonException;
use function PHPUnit\Framework\containsIdentical;

class Weather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Assistant:Weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '助手每日天气播报';

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

        //天行数据
        $Api_Tianxing = new Tianxing(QBotDB::getConfig('Api', '天行数据->apiKey'));
        //检索用户
        $user = DB::table('users')
            ->where('user_data->助手系统->我的助手->启用', true)
            ->whereNotNull('user_data->助手系统->我的助手->城市')
            ->where('user_data->助手系统->我的助手->到期时间', '>=', time());

        $user->get()->each(
            static function ($item) use ($qbot, $Api_Tianxing) {
                try {
                    $city = json_decode($item->user_data, false, 512, JSON_THROW_ON_ERROR)->助手系统->我的助手->城市;
                } catch (JsonException) {
                    return;
                }
                $result = $Api_Tianxing->weather($city);
                if ($result->code === 200) {
                    $weather=$result->data->getNextData();
                    if ($weather !== null) {
                        $str="{$weather->area}今日{$weather->weather}\n"
                        ."最高气温{$weather->highest}，最低气温{$weather->lowest}\n"
                        ."当前气温{$weather->real}，降雨概率{$weather->pop}%";
                        $qbot->send_private_msg($item->user_id, '这是小梦刚才为您整理的今日天气');
                        $qbot->send_private_msg($item->user_id, $str);
                        $qbot->send_private_msg($item->user_id, '小梦提醒您：');
                        $qbot->send_private_msg($item->user_id, $weather->tips);
                    }
                }
            });
        return 0;
    }
}
