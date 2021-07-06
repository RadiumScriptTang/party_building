<?php

namespace app\dal\model;

use think\Model;

class Attempt extends Model
{
    protected $autoWriteTimestamp = true;

    private static $all_ranks = ["一心一意", "再接再厉", "三省吾身", "名扬四海", "学富五车", "六韬三略", "七步才华", "才高八斗", "九天揽月", "十年磨剑"];


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

    public static function countByPass($final_level, $final_correct, $final_time, $rank_id)
    {
        // 先判断 最终等级低于 我的等级
        $n_bypass = Attempt::countNum([
            "is_last" => ["eq", true],
            "is_complete" => ["eq", true],
            "rank_id" => ["lt", $rank_id]
        ]);
        // 最终等级跟我一样但没我对的多
        $n_bypass += Attempt::countNum([
            "is_last" => true,
            "is_complete" => true,
            "rank_id" => $rank_id,
            "final_correct" => ["lt", $final_correct],
        ]);
        // 最终等级跟我一样，对的跟我一样多，但是用时比我长
        $n_bypass += Attempt::countNum([
            "is_last" => true,
            "is_complete" => true,
            "rank_id" => $rank_id,
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

    public static function countByPassDomestic($final_level, $final_correct, $final_time, $rank_id)
    {
        // 先判断 最终等级低于 我的等级
        $n_bypass = Attempt::countNumDomestic([
            "is_last" => ["eq", true],
            "is_complete" => ["eq", true],
            "rank_id" => ["lt", $rank_id]
        ]);
        // 最终等级跟我一样但没我对的多
        $n_bypass += Attempt::countNumDomestic([
            "is_last" => true,
            "is_complete" => true,
            "rank_id" => $rank_id,
            "final_correct" => ["lt", $final_correct],
        ]);
        // 最终等级跟我一样，对的跟我一样多，但是用时比我长
        $n_bypass += Attempt::countNumDomestic([
            "is_last" => true,
            "is_complete" => true,
            "rank_id" => $rank_id,
            "final_correct" => $final_correct,
            "final_time" => ["gt", $final_time]
        ]);
        return $n_bypass;
    }


    public function getRankIdAttr($rank)
    {

        if ($rank >= 0 && $rank <= 9){
            return self::$all_ranks[$rank];
        }
        return "一心一意";
    }
}