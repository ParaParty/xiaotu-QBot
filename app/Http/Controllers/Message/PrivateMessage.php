<?php

namespace App\Http\Controllers\Message;

use App\Class\Api\OwnThink\OwnThink;
use App\Class\Api\Tianxing\Tianxing;
use App\Class\QBotDB;
use App\Class\QBotHttpApi;

use App\Class\QBotRequest\private_message;
use App\Class\QBotReturn\send_private_msg;
use App\Class\TCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;

class PrivateMessage extends Controller
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
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));

        //收到的请求
        $fromData = new private_message($request->input());

        //Api接口
        //天行数据
        $Api_Tianxing = new Tianxing(QBotDB::getConfig('Api', '天行数据->apiKey'));
        //思知
        $Api_OwnThink = new OwnThink(QBotDB::getConfig('Api', '思知->Appid'));
        #endregion

        #region 命令分割
        $cmd = explode(' ', $fromData->message);
        #endregion

        #region 助手系统

        if ($cmd[0] === '绑定城市' && isset($cmd[1])) {
            $result = $Api_Tianxing->weather($cmd[1]);
            if ($result->code !== 200
                || ($weather = $result->getNextData()) === null) {
                return $qbot->rapidResponse('绑定失败，该城市不存在');
            }
            QBotDB::setUserData($fromData->user_id, '助手系统->我的助手->城市', $weather->area);
            return $qbot->rapidResponse('成功绑定至' . $weather->area);
        }

        #endregion

        #region 智能闲聊
        $interface = QBotDB::getConfig('娱乐系统', '智能闲聊->接口');
        switch ($interface) {
            case '天行数据':
                $robot = $Api_Tianxing->robot($fromData->message, (string)$fromData->user_id);
                if ($robot->code !== 200) {
                    return $qbot->rapidResponse('系统繁忙请稍后再试，如若多次出现请联系管理员');
                }
                if (($data = $robot->getNextData())->type !== 'text') {
                    return $qbot->rapidResponse('暂不支持的返回类型，正在开发中');
                }
                return $qbot->rapidResponse($data->reply);
            case '思知':
                $robot = $Api_OwnThink->question($fromData->message, (string)$fromData->user_id);
                if ($robot->message !== 'success') {
                    return $qbot->rapidResponse('系统繁忙请稍后再试，如若多次出现请联系管理员');
                }
                if ($robot->type !== 5000) {
                    return $qbot->rapidResponse('暂不支持的返回类型，正在开发中');
                }
                return $qbot->rapidResponse($robot->info->text);
            default:
                return $qbot->rapidResponse($fromData->message);
        }
        #endregion

    }
}
