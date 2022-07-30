<?php

namespace App\Class;

class CQ
{
    public static function at(int $qq): string
    {
        return self::makeCQ_code('at', ['qq' => $qq]);
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
}
