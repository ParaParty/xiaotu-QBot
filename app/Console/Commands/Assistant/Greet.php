<?php

namespace App\Console\Commands\Assistant;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\TCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Greet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Assistant:Greet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '小助手的问候';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time=time();
        $tNow=date('H:i',$time);
        $t=[
            '00:00'=>'午夜了，有什么事明天再做吧，身体最重要，小梦熬不住了，先睡了',
            '06:00'=>'早安，今天又是元气满满的一天',
            '12:00'=>'中午好，记得吃饭哦！小梦的肚子已经饿得咕咕叫了',
            '12:30'=>'叮咚！午休时间到，午安！',
            '18:00'=>'傍晚了，放松一下，看看窗外',
            '22:00'=>'已经很晚了，主人注意身体早点休息哦，小梦再陪主人熬一会儿',
        ];
        if (isset($t[$tNow])) {
            //Http Api
            $qbot = new QBotHttpApi(
                QBotDB::getConfig('system', 'http_address'),
                QBotDB::getConfig('system', 'http_access_token'));
            //检索用户
            $user = DB::table('users')
                ->where('user_data->助手系统->我的助手->启用', true)
                ->where('user_data->助手系统->我的助手->到期时间', '>=', time());

            $user->get()->each(
                static function ($item) use ($qbot,$t,$tNow) {
                    $qbot->send_private_msg($item->user_id, $t[$tNow]);
                });
        }




        return 0;
    }
}
