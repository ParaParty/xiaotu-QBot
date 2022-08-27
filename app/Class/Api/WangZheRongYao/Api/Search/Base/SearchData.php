<?php

namespace App\Class\Api\WangZheRongYao\Api\Search\Base;

class SearchData
{

    /**
     * @var string 游戏昵称
     */
    public string $nickName;

    /**
     * @var int 角色ID
     */
    public int $id;

    /**
     * @var int 原始角色ID
     */
    public int $raw_id;

    /**
     * @var string 性别
     */
    public string $gender;

    /**
     * @var string 在线状态
     */
    public string $status;

    /**
     * @var string 上次在线时间
     */
    public string $lastOnlineTime;

    /**
     * @var int 游戏等级
     */
    public int $level;

    /**
     * @var int 信誉等级
     */
    public int $creditLevel;

    /**
     * @var string 贵族等级 未知为 ***
     */
    public string $vipLevel;

    /**
     * @var string 所在区服
     */
    public string $block;

    /**
     * @var string 段位
     */
    public string $dan;

    /**
     * @var int 巅峰积分
     */
    public int $danScore;

    /**
     * @var string 英雄 已拥有/未拥有
     */
    public string $heroSum;

    /**
     * @var string 皮肤 已拥有/未拥有
     */
    public string $skinSum;

    /**
     * @var int 游戏场次
     */
    public int $gameSum;

    /**
     * @var string 胜率
     */
    public string $victoryRate;

    /**
     * @var float 评分
     */
    public float $score;

    /**
     * @var int 金牌
     */
    public int $goldMedalSum;

    /**
     * @var int 银牌
     */
    public int $silverMedalSum;

    /**
     * @var int 当前赛季最高连胜
     */
    public int $nowVictoryMax;

    /**
     * @var string 历史表现（Web URL）
     */
    public string $history;

    /**
     * @var string 游戏头像（图片URL）
     */
    public string $icon;

    /**
     * @var array 常用英雄[3]{hero,score,victoryRate,usageSum}
     */
    public array $likeHeroes;

    public function __construct($data)
    {
        $data = $data['主要信息'];
        $this->nickName = $data['游戏名字'];
        $this->id = (int)$data['角色ID'];
        $this->raw_id = (int)$data['原始角色ID'];
        $this->gender = $data['性别'];
        $this->status = $data['游戏'];
        $this->lastOnlineTime = $data['最近离线时间'];
        $this->level = (int)$data['游戏等级'];
        $this->creditLevel = (int)$data['信誉等级'];
        $this->vipLevel = $data['贵族等级'];
        $tmp = explode(' ', $data['所属区和段位']);
        $this->block = $tmp[0];
        unset($tmp[0]);
        $this->dan = implode(' ', $tmp);
        $this->danScore = (int)$data['巅峰赛分'];
        $this->heroSum = $data['英雄'];
        $this->skinSum = $data['皮肤'];
        $this->gameSum = (int)$data['游戏场次'];
        $this->victoryRate = $data['胜率'];
        $this->score = (float)$data['评分'];
        $this->goldMedalSum = (int)$data['金牌'];
        $this->silverMedalSum = (int)$data['银牌'];
        $this->nowVictoryMax = (int)$data['赛季最高连胜'];
        $this->history = $data['历史表现'];
        $this->icon = $data['游戏头像'];
        foreach ($data['常用英雄'] as $item) {
            $hero = (object)[];
            $hero->hero = $item['英雄名'];
            $hero->score = $item['战力'];
            $hero->victoryRate = $item['胜率'];
            $hero->usageSum = $item['场次'];
            $this->likeHeroes[] = $hero;
        }
    }

}
