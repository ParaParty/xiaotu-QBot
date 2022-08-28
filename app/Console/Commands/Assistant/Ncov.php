<?php

namespace App\Console\Commands\Assistant;

use App\Class\Api\Tianxing\Tianxing;
use App\Class\QBotDB;
use App\Class\QBotHttpApi;
use App\Class\TCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use JsonException;

class Ncov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Assistant:Ncov';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '新冠疫情提示';

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
            ->where('user_data->助手系统->我的助手->到期时间', '>=', time());
        $result = $Api_Tianxing->ncov();
        if ($result->code === 200) {
            $ncov = $result->data->getNextData();
            if ($ncov !== null) {
                $data['更新时间'] = date('Y年m月d日H时', $ncov->desc->modifyTime);

                $user->get()->each(
                    static function ($item) use ($qbot, $ncov, $data) {
                        $str = "小梦查询到，截止{$data['更新时间']}，国内有高风险区{$ncov->desc->highDangerCount}个，"
                            . "中风险区{$ncov->desc->midDangerCount}个，疫情凶险反复无常，出行一定要注意安全哦！";
                        $qbot->send_private_msg($item->user_id, $str);
                        try {
                            $city = json_decode($item->user_data, false, 512, JSON_THROW_ON_ERROR)->助手系统->我的助手->城市;
                        } catch (JsonException) {
                            $qbot->send_private_msg($item->user_id, '您还没有绑定城市，小梦无法为您查询风险地区，'
                                . '请先绑定城市哦！绑定格式：绑定城市 城市，例如：绑定城市 西安');
                            return 0;
                        }
                        $areaData = [
                            'high' => [],
                            'mid' => []
                        ];
                        foreach ($ncov->riskarea->high as $value) {
                            if (($i = strpos($value, "·{$city}·")) !== false) {
                                $areaData['high'][] = substr($value, $i + strlen("·{$city}·"));
                            }
                        }
                        foreach ($ncov->riskarea->mid as $value) {
                            if (($i = strpos($value, "·{$city}·")) !== false) {
                                $areaData['mid'][] = substr($value, $i + strlen("·{$city}·"));
                            }
                        }
                        if (count($areaData, 1) === 2) {
                            $qbot->send_private_msg($item->user_id, '小梦没有在绑定城市查到风险区，好棒耶~');
                            return 0;
                        }
                        $qbot->send_private_msg($item->user_id, '小梦查到了绑定城市有高风险区'
                            . count($areaData['high']) . '个，中风险区' . count($areaData['mid']) . '个');
                        $high = implode('<br>', $areaData['high']);
                        if ($high === '') {
                            $high = '无';
                        }
                        $mid = implode('<br>', $areaData['mid']);
                        if ($mid === '') {
                            $mid = '无';
                        }
                        $str="<h1>高风险区：<br>$high<hr>中风险区：<br>$mid</h1>";
                        do {
                            $key=bin2hex(random_bytes(3));
                        } while (Cache::has($key));
                        Cache::put($key,$str,now()->addHours(24));
                        $qrCode = QrCode::create(URL::to('/')."/text/$key")
                            ->setEncoding(new Encoding('UTF-8'))
                            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                            ->setSize(200)
                            ->setMargin(10)
                            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                            ->setForegroundColor(new Color(0, 0, 0))
                            ->setBackgroundColor(new Color(255, 255, 255));
                        $writer = new PngWriter();
                        $img = 'base64://' . substr($writer->write($qrCode)->getDataUri(), 22);
                        $qbot->send_private_msg($item->user_id, '查看风险地区：'.TCode::image($img));
                        return 0;
                    });
            }
        }
        return 0;
    }
}
