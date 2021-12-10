<?php
namespace app\dal\model;
use think\Model;

class User extends Model
{
    public static function get_id_list()
    {
        $data = [];
        $query = static::parseQuery($data, null, false);
        return $query->column("id");
    }

    public static function count_all()
    {
        $data = [];
        $query = static::parseQuery($data, null, false);
        return $query->count();
    }

    public static function count_valid()
    {
        $data = [];
        $query = static::parseQuery($data, null, false);
        return $query->where(["name" => ["<>", ""]])->count();
    }
}