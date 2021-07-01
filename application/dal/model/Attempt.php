<?php

namespace app\dal\model;

use think\Model;

class Attempt extends Model
{
    protected $autoWriteTimestamp = true;

    private static function countNum($data)
    {
        $query = static::parseQuery($data, null, false);
        return $query->where($data)->count();
    }

    private static function countNumDomestic($data)
    {
        $query = static::parseQuery($data, null, false);
        return $query->join("user", "user.id = attempt.user_id and user.region = '中国(亚洲)' ")->where($data)->count();
    }

    public static function countAllValidAttempt()
    {
        return Attempt::countNum([
            "is_last" => true,
            "is_complete" => true
        ]);
    }

    public static function countByPass($final_level, $final_correct, $final_time)
    {
        // 先判断 最终等级低于 我的等级
        $n_bypass = Attempt::countNum([
            "is_last" => ["eq", true],
            "is_complete" => ["eq", true],
            "final_level" => ["lt", $final_level]
        ]);
        // 最终等级跟我一样但没我对的多
        $n_bypass += Attempt::countNum([
            "is_last" => true,
            "is_complete" => true,
            "final_level" => $final_level,
            "final_correct" => ["lt", $final_correct],
        ]);
        // 最终等级跟我一样，对的跟我一样多，但是用时比我长
        $n_bypass += Attempt::countNum([
            "is_last" => true,
            "is_complete" => true,
            "final_level" => $final_level,
            "final_correct" => $final_correct,
            "final_time" => ["gt", $final_time]
        ]);
        return $n_bypass;
    }

    public static function countValidDomestic()
    {
        return Attempt::countNumDomestic([
            "is_last" => true,
            "is_complete" => true
        ]);
    }

    public static function countByPassDomestic($final_level, $final_correct, $final_time)
    {
        // 先判断 最终等级低于 我的等级
        $n_bypass = Attempt::countNumDomestic([
            "is_last" => ["eq", true],
            "is_complete" => ["eq", true],
            "final_level" => ["lt", $final_level]
        ]);
        // 最终等级跟我一样但没我对的多
        $n_bypass += Attempt::countNumDomestic([
            "is_last" => true,
            "is_complete" => true,
            "final_level" => $final_level,
            "final_correct" => ["lt", $final_correct],
        ]);
        // 最终等级跟我一样，对的跟我一样多，但是用时比我长
        $n_bypass += Attempt::countNumDomestic([
            "is_last" => true,
            "is_complete" => true,
            "final_level" => $final_level,
            "final_correct" => $final_correct,
            "final_time" => ["gt", $final_time]
        ]);
        return $n_bypass;
    }
}