<?php

namespace App\Class;

class TCode
{
    //QQ小表情
    public static array $face = [
        '太阳' => 74


    ];

    public static function replace(string $str, array $data = []): string
    {
        preg_match_all('/(?<!\\\\)\\{.+?(?!\\\\)\\}/', $str, $arr, PREG_UNMATCHED_AS_NULL);
        foreach ($arr[0] as $key) {
            $key2 = substr($key, 1, -1);
            if (isset($data[$key2])) {
                //提交参数
                $str = str_replace($key, $data[$key2], $str);
            } elseif (isset(self::$face[$key2])) {
                //QQ小表情
                $str = str_replace($key, self::makeCQ_code('face', [
                    'id' => self::$face[$key2]
                ]), $str);
            } elseif ($setting = QBotDB::getConfig($key2)) {
                $str = str_replace($key, $setting, $str);
            }

        }
        //艾特
        preg_match_all('/(?<!\\\\)@@.+?(?!\\\\)@/', $str, $arr, PREG_UNMATCHED_AS_NULL);
        foreach ($arr[0] as $key) {
            $key2 = substr($key, 2, -1);
            if ((string)(int)$key2 === $key2) {
                $str = str_replace($key, "[@$key2]", $str);
            }
        }
        //@转义处理
        return str_replace(array('\\@', '[@QQ]'), array('@', '\\u005b@QQ]'), $str);
    }

    private static function makeCQ_code(string $name, array $data): string
    {
        $str = "[CQ:$name";
        foreach ($data as $key => $value) {
            $str .= ",$key=$value";
        }
        $str .= ']';
        return $str;
    }

    public static function at(int $qq): string
    {
        return self::makeCQ_code('at', ['qq' => $qq]);
    }
}
