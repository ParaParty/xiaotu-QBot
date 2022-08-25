<?php

namespace App\Class;

/**
 *
 */
class TCode
{
    //屎山改，能润就行


    public static array $face = [
        //系统表情
        'system_旭日币' => 74,
        'system_旭日勋章' => '\ud83c\udf96\ufe0f',


        //QQ小表情
        'face_太阳' => 74,
        'face_鼓掌' => 99,
        'face_庆祝' => 144,


        //emoji
        'emoji_排名_1' => '\ud83e\udd47',
        'emoji_排名_2' => '\ud83e\udd48',
        'emoji_排名_3' => '\ud83e\udd49',
        'emoji_排名_4+' => '\ud83c\udfc5',

        'emoji_钟表' => '\ud83d\udd70\ufe0f',


    ];

    //屎山改，能润就行
    public static function replace(string $str, array $data = []): string
    {
        $str = str_replace('<br>', "\n", $str);
        preg_match_all('/(?<!\\\\){.+?(?<!\\\\)}/', $str, $arr, PREG_UNMATCHED_AS_NULL);
        foreach ($arr[0] as $key) {
            $key2 = substr($key, 1, -1);
            if (isset($data[$key2])) {
                //提交参数
                $str = str_replace($key, $data[$key2], $str);
            } elseif ($key2[0] === '@') {
                if (is_numeric($qq = substr($key2, 1))) {
                    //QQ艾特
                    //懒地判断是不是标准QQ号了，毕竟屎山改，能润就行
                    $str = str_replace($key, self::at($qq), $str);
                }
            } elseif (isset(self::$face[$key2])) {
                if (is_numeric(self::$face[$key2])) {
                    //QQ小表情
                    $str = str_replace($key, self::makeCQ_code('face', [
                        'id' => self::$face[$key2]
                    ]), $str);
                } else {
                    //原文（暂定）
                    $str = str_replace($key, self::$face[$key2], $str);
                }
            }

        }
        //反转义处理
        return str_replace(array('\\{', '\\}'), array('{', '}'), $str);
    }

    /**
     * @param int $qq
     * @return string
     */
    public static function at(int $qq): string
    {
        return self::makeCQ_code('at', ['qq' => $qq]);
    }

    /**
     * @param int $text
     * @return string
     */
    public static function tts(int $text): string
    {
        return self::makeCQ_code('tts', ['text' => $text]);
    }

    /**
     * 标准音乐分享卡片
     * @param string $type 类型 qq：QQ音乐 163：网易云音乐 xm：虾米音乐
     * @param string $id 歌曲id
     * @return string
     */
    public static function music(string $type, string $id): string
    {
        return self::makeCQ_code('music', ['type' => $type,'id'=>$id]);
    }

    /**
     * @param string $name
     * @param array $data
     * @return string
     */
    public static function makeCQ_code(string $name, array $data): string
    {
        $str = "[CQ:$name";
        foreach ($data as $key => $value) {
            $str .= ",$key=$value";
        }
        $str .= ']';
        return $str;
    }

    /**
     * @param string $url
     * @param int $cache
     * @param string $type
     * @param int $subType
     * @return string
     */
    public static function image(string $url, int $cache = 0, string $type = '', int $subType = 0): string
    {
        $param = [
            'file' => $url
        ];
        switch ($type) {
            case '':
                $param['subtype'] = $subType;
                break;
            case 'flash':
                $param['type'] = $type;
                break;
            case 'show':
                $param['type'] = $type;
                $param['id'] = $subType;
                break;
        }
        if ($cache !== 0) {
            $param['cache']=$cache;
        }
        return self::makeCQ_code('image', $param);
    }
}
