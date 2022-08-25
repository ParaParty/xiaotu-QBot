<?php

namespace App\Http\Controllers\Message;

use App\Class\Api\OwnThink\OwnThink;
use App\Class\Api\Tianxing\Tianxing;
use Exception;
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

use App\Class\Api\WangZheRongYao\WangZheRongYao;
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
     * @param Request $request
     * @return array|string
     * @throws Exception
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
        //天行数据
        $Api_Tianxing = new Tianxing(QBotDB::getConfig('Api', '天行数据->apiKey'));
        //思知
        $Api_OwnThink = new OwnThink(QBotDB::getConfig('Api', '思知->Appid'));
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
                QBotDB::operate_medal($fromData->group_id, $fromData->user_id, $checkinData['旭日勋章']);
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
                . '{system_旭日勋章}' . QBotDB::operate_medal($fromData->group_id, $fromData->user_id) . "{system_旭日勋章}"
            );

        }
        #endregion

        #region 娱乐系统

        if ($fromData->message === '智能闲聊') {
            $interface = QBotDB::getConfig('娱乐系统', '智能闲聊->接口');
            $str = "{face_打call}智能闲聊{face_打call}\n\n"
                . "{face_打call}当前接口{face_打call}\n"
                . "{emoji_叉}天行数据{emoji_叉}\n"
                . "{emoji_叉}思知{emoji_叉}";
            $str=str_replace("{emoji_叉}$interface{emoji_叉}","{emoji_圈}$interface{emoji_圈}",$str);
            return $qbot->rapidResponse($str);
        }

        //智能闲聊
        if ($cmd[0] === TCode::at($fromData->self_id) && count($cmd) >= 2) {
            DB::table('speech')
                ->where('user_id', $fromData->user_id)
                ->where('message_id', $fromData->message_id)
                ->update(['type' => 2]);
            $tmp = $cmd;
            unset($tmp[0]);
            $question = trim(implode(' ', $tmp));
            $interface = QBotDB::getConfig('娱乐系统', '智能闲聊->接口');
            switch ($interface) {
                case '天行数据':
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
                case '思知':
                    $robot = $Api_OwnThink->question($question, (string)$fromData->user_id);
                    if ($robot->message !== 'success') {
                        return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 系统繁忙请稍后再试，如若多次出现请联系管理员');
                    }
                    if ($robot->type !== 5000) {
                        return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 暂不支持的返回类型，正在开发中');
                    }
                    return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' ' . $robot->info->text);
                default:
                    return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' ' . $question);
            }
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
            $source = QBotDB::getConfig('便民系统', '点歌->音源');
            $song = new Meting($source);
            try {
                $search = json_decode($song->format(true)->search($songName), false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 系统错误');
            }
            $source_str = [
                'netease' => '网易云音乐',
                'tencent' => 'QQ音乐',
                'kugou' => '酷狗音乐',
                'kuwo' => '酷我音乐',
                'xiami' => '虾米音乐',
                'baidu' => '百度音乐'
            ];
            $source_card = [
                'netease' => '163',
                'tencent' => 'qq',
                'xiami' => 'xm'
            ];
            if (!isset($search[0])) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . " 未在当前音源{$source_str[$source]}中搜索到歌曲");
            }
            $price = QBotDB::getConfig('便民系统', '点歌->价格', true);
            $ret = QBotDB::operate_price($fromData->group_id, $fromData->user_id, $price);
            if ($ret !== true) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $ret);
            }
            return $qbot->rapidResponse(TCode::music($source_card[$source], $search[0]->id));
        }

        //王者查询
        if ($cmd[0] === '王者查询' && count($cmd) >= 2) {
            $tmp = $cmd;
            unset($tmp[0]);
            $nickName = trim(implode(' ', $tmp));
            if (strlen($nickName) > 18) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 超出长度限制。');
            }
            $price = QBotDB::getConfig('便民系统', '王者查询->价格', true);
            $ret = QBotDB::operate_price($fromData->group_id, $fromData->user_id, $price);
            if ($ret !== true) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . $ret);
            }
            $search = WangZheRongYao::search_other($nickName);
            if ($search === false) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 未知错误');
            }
            if ($search === null) {
                return $qbot->rapidResponse(TCode::at($fromData->user_id) . ' 未查询到玩家');
            }
            while (($player = $search->getNextData()) !== null) {
                $str = TCode::image($player->icon) . "\n";
                $str .= "游戏昵称：$player->nickName\n";
                $str .= "所在区服：$player->block\n";
                $str .= "性别：$player->gender\n";
                $str .= "游戏状态：$player->status\n";
                $str .= "角色等级：$player->level\n";
                $str .= "信誉等级：$player->creditLevel\n";
                $str .= "贵族等级：$player->vipLevel\n";
                $str .= "游戏场次：$player->gameSum\n";
                $str .= "胜率：$player->victoryRate\n";
                $str .= "当前段位：$player->dan\n";
                $str .= "巅峰积分：$player->danScore\n";
                $str .= "本赛季最高连胜：$player->nowVictoryMax\n";
                $str .= "最近离线时间：\n$player->lastOnlineTime\n";
                $str .= "常用英雄：\n";
                foreach ($player->likeHeroes as $hero) {
                    $str .= "@$hero->hero\n";
                    $str .= "##战力：$hero->score\n";
                    $str .= "##场次：$hero->usageSum\n";
                    $str .= "##胜率：$hero->victoryRate\n";
                }
                $str .= "历史表现：\n";

                $qrCode = QrCode::create($player->history)
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                    ->setSize(600)
                    ->setMargin(10)
                    ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->setForegroundColor(new Color(0, 0, 0))
                    ->setBackgroundColor(new Color(255, 255, 255));
                $writer = new PngWriter();
                $img = 'base64://' . substr($writer->write($qrCode)->getDataUri(), 22);

                $str .= TCode::image($img);
                $result[] = $str;
            }
            $qbot->send_group_msg($fromData->group_id, TCode::at($fromData->user_id) . ' 已私聊发送查询结果');
            foreach ($result as $item) {
                $qbot->send_private_msg($fromData->user_id, $item);
            }
            return [];
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
