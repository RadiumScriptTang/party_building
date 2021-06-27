<?php
namespace app\dal\model;
use think\Model;

class Question extends Model
{
    public static function get_random($level)
    {
        $data = [
            "level" => $level
        ];
        $query = static::parseQuery($data, null, false);
        return $query->orderRaw("rand()")->limit(1)->find($data);
    }
}