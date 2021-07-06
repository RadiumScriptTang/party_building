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
}