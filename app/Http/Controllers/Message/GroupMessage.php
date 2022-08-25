<?php

namespace App\Http\Controllers\Message;

use JsonException;
use Metowolf\Meting;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

use App\Class\Api\Tianxing\tianxing;
use App\Class\TCode;
use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\QBotRequest\group_message;
use App\Class\QBotRequest\private_message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tags\Source;

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
        //Http Api
        $qbot = new QBotHttpApi(
            QBotDB::getConfig('system', 'http_address'),
            QBotDB::getConfig('system', 'http_access_token'));
        //消息数据
        $fromData = new group_message($request->input());
        //Api接口
        //天行Api
        $Api_Tianxing = new Tianxing(QBotDB::getConfig('Api', '天行数据->apiKey'));
        #endregion

        #region 发言统计
        QBotDB::setSpeech([
            'user_id' => $fromData->user_id,
            'group_id' => $fromData->group_id,
            'message' => $fromData->message,
            'message_id' => $fromData->message_id,
            'datetime' => date('Y-m-d H:i:s'),
            'type' => 1
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
        if ($fromData->message === '签到') {
            $tmp = date('Y-m-d', $fromData->time);
            if (!$checkinRecord = QBotDB::getUserData($fromData->user_id, "签到系统->签到记录->$tmp", true)) {
                $checkinConfig = QBotDB::getConfig('签到系统', '', true);
                $checkinData = [
                    '旭日币' => random_int($checkinConfig->旭日币->下限, $checkinConfig->旭日币->上限),
                    '旭日勋章' => random_int($checkinConfig->旭日勋章->下限, $checkinConfig->旭日勋章->上限),
                    '时间' => $fromData->time
                ];
                QBotDB::setUserData($fromData->user_id, "签到系统->签到记录->$tmp", $checkinData);
                QBotDB::operate_money($fromData->group_id, $fromData->user_id, $checkinData['旭日币'], $fromData->time);
                QBotDB::operate_detail($fromData->group_id, $fromData->user_id, $checkinData['旭日勋章']);
                $tmp = DB::table('users')
                    ->whereNotNull("user_data->签到系统->签到记录->$tmp")
                    ->count();
                $tmp2 = $tmp >= 4 ? '4+' : (string)$tmp;
                return $qbot->rapidResponse(
                    TCode::at($fromData->user_id) . " \n签到成功，获得：\n"
                    . "{system_旭日币}{$checkinData['旭日币']}{system_旭日币}\n"
                    . "{system_旭日勋章}{$checkinData['旭日勋章']}{system_旭日勋章}\n"
                    . "今天{emoji_排名_$tmp2}第{$tmp}位签到者{emoji_排名_$tmp2}"
                );
            }
            return $qbot->rapidResponse(
                TCode::at($fromData->user_id) . "\n"
                . "您今日已签到，获得：\n"
                . "{system_旭日币}$checkinRecord->旭日币{system_旭日币}\n"
                . "{system_旭日勋章}$checkinRecord->旭日勋章{system_旭日勋章}\n"
                . '{emoji_钟表}' . date('Y-m-d H:i:s', $checkinRecord->时间) . '{emoji_钟表}'
            );


        }

        if ($fromData->message === '查询') {
            return $qbot->rapidResponse(
                TCode::at($fromData->user_id) . "\n"
                . "余额：\n"
                . '{system_旭日币}' . QBotDB::operate_money($fromData->group_id, $fromData->user_id) . "{system_旭日币}\n"
                . '{system_旭日勋章}' . QBotDB::operate_detail($fromData->group_id, $fromData->user_id) . "{system_旭日勋章}"
            );

        }
        #endregion

        #region 娱乐系统
        //智能闲聊
        if ($cmd[0] === TCode::at($fromData->self_id) && count($cmd) >= 2) {
            $tmp = $cmd;
            unset($tmp[0]);
            $question = trim(implode(' ', $tmp));
            $robot = $Api_Tianxing->robot($question, (string)$fromData->user_id);
            if ($robot->code !== 200) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 系统繁忙请稍后再试，如若多次出现请联系管理员');
            }
            if (($data = $robot->getNextData())->type !== 'text') {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 暂不支持的返回类型，正在开发中');
            }
            if (!str_contains($data->reply, '<br>')) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $data->reply);
            }
            return $qbot->rapidResponse(TCode::at($fromData->user_id) . "\n"
                . $data->reply);
        }

        //随机图片
        if ($fromData->message === '来张图片') {
            $price = QBotDB::getConfig('娱乐系统', '随机图片->来张图片->价格', true);
            $ret = QBotDB::operate_price($fromData->group_id, $fromData->user_id, $price);
            if ($ret !== true) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $ret);
            }
            return $qbot->rapidResponse(TCode::image('https://api.ixiaowai.cn/api/api.php?'
                . md5($fromData->user_id . $fromData->message_id . $fromData->time)));
        }
        #endregion

        #region 便民系统

        //二维码生成
        if ($cmd[0] === '二维码生成' && count($cmd) >= 2) {
            $tmp = $cmd;
            unset($tmp[0]);
            $string = trim(implode(' ', $tmp));
            if (strlen($string) > 400) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 超出长度限制。');
            }
            $price = QBotDB::getConfig('便民系统', '二维码生成->价格', true);
            $price->旭日币 *= strlen($string) + 100;
            $price->旭日勋章 *= (int)(strlen($string) / 100) + 1;
            $ret = QBotDB::operate_price($fromData->group_id, $fromData->user_id, $price);
            if ($ret !== true) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $ret);
            }
            $qrCode = QrCode::create($string)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(600)
                ->setMargin(10)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
            $writer = new PngWriter();
            $img = 'base64://' . substr($writer->write($qrCode)->getDataUri(), 22);
            return $qbot->rapidResponse(TCode::image($img));
        }

        //点歌
        if ($cmd[0] === '点歌' && count($cmd) >= 2) {
            $tmp = $cmd;
            unset($tmp[0]);
            $songName = trim(implode(' ', $tmp));
            $source=QBotDB::getConfig('便民系统', '点歌->音源');
            $song = new Meting($source);
            try {
                $search = json_decode($song->format(true)->search($songName), false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id).' 系统错误');
            }
            $source_str=[
                'netease'=>'网易云音乐',
                'tencent'=>'QQ音乐',
                'kugou'=>'酷狗音乐',
                'kuwo'=>'酷我音乐',
                'xiami'=>'虾米音乐',
                'baidu'=>'百度音乐'
            ];
            $source_card=[
                'netease'=>'163',
                'tencent'=>'qq',
                'xiami'=>'xm'
            ];
            if (!isset($search[0])) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id)." 未在当前音源{$source_str[$source]}中搜索到歌曲");
            }
            $price = QBotDB::getConfig('便民系统', '点歌->价格', true);
            $ret = QBotDB::operate_price($fromData->group_id, $fromData->user_id, $price);
            if ($ret !== true) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $ret);
            }
            return $qbot->rapidResponse(TCode::music($source_card[$source],$search[0]->id));
        }
        #endregion

        #region 其他
        if ($fromData->message === '作死') {
            $ban = random_int(20, 80);
            $qbot->set_group_ban($fromData->group_id, $fromData->user_id, $ban);
            return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' {face_鼓掌}恭喜作死成功，获得' . $ban . '秒禁言。');
        }
        #endregion


        //没有触发命令，修改发言统计 type 字段;
        DB::table('speech')
            ->where('user_id', $fromData->user_id)
            ->where('message_id', $fromData->message_id)
            ->update(['type' => 0]);
        return [];
    }
}
