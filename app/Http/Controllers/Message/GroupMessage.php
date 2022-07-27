<?php

namespace App\Http\Controllers\Message;

use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\QBotRequest\group_message;
use App\Class\QBotRequest\private_message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GroupMessage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return array|string
     */
    public function __invoke(Request $request): array|string
    {

        #region 开始
        //时间戳
        $time = time();
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));
        //消息数据

        $fromData = new group_message($request->input());
        #endregion

        #region 发言统计
        QBotDB::setSpeech([
            'user_id' => $fromData->user_id,
            'group_id' => $fromData->group_id,
            'message' => $fromData->message,
            'message_id' => $fromData->message_id,
            'datetime' => date('Y-m-d H:i:s')
        ]);
        #endregion

        #region 菜单检索
        if ($menu = QBotDB::getMenu($fromData->message)) {
            return $qbot->rapidResponse($menu);
        }
        #endregion

        #region 命令分割
        $cmd = explode(' ', $fromData->message);
        #endregion

        #region 签到系统


        #endregion




        //没有触发命令，修改发言统计'is_cmd'字段;
        DB::table('speech')
            ->where('user_id',$fromData->user_id)
            ->where('message_id',$fromData->message_id)
            ->update(['is_cmd'=>false]);
        return [];
    }
}