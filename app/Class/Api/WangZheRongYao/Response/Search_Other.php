<?php

namespace App\Class\Api\WangZheRongYao\Response;

use App\Class\Api\WangZheRongYao\BaseResponse\_base_search_other;

class Search_Other
{
    /**
     * @var object 网站返回数据
     */
    private array $data;
    /**
     * @var int 索引
     */
    private int $i = 0;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return ?_base_search_other
     */
    public function getNextData(): ?_base_search_other
    {
        if (!isset($this->data[$this->i])) {
            return null;
        }
        $data = $this->data[$this->i]['主要信息'];
        $ret = new _base_search_other();
        $ret->nickName = $data['游戏名字'];
        $ret->id = (int)$data['角色ID'];
        $ret->raw_id = (int)$data['原始角色ID'];
        $ret->gender = $data['性别'];
        $ret->status = $data['游戏'];
        $ret->lastOnlineTime = $data['最近离线时间'];
        $ret->level = (int)$data['游戏等级'];
        $ret->creditLevel = (int)$data['信誉等级'];
        $ret->vipLevel = $data['贵族等级'];
        $tmp=explode(' ',$data['所属区和段位']);
        $ret->block = $tmp[0];
        unset($tmp[0]);
        $ret->dan = implode(' ',$tmp);
        $ret->danScore = (int)$data['巅峰赛分'];
        $ret->heroSum = $data['英雄'];
        $ret->skinSum = $data['皮肤'];
        $ret->gameSum = (int)$data['游戏场次'];
        $ret->victoryRate = $data['胜率'];
        $ret->score = (float)$data['评分'];
        $ret->goldMedalSum = (int)$data['金牌'];
        $ret->silverMedalSum = (int)$data['银牌'];
        $ret->nowVictoryMax = (int)$data['赛季最高连胜'];
        $ret->history= $data['历史表现'];
        $ret->icon = $data['游戏头像'];
        foreach ($data['常用英雄'] as $item) {
            $hero=(object)[];
            $hero->hero=$item['英雄名'];
            $hero->score=$item['战力'];
            $hero->victoryRate=$item['胜率'];
            $hero->usageSum=$item['场次'];
            $ret->likeHeroes[] = $hero;
        }
        $this->i++;
        return $ret;
    }
}
