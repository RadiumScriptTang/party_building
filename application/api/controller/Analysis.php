<?php

namespace app\api\controller;

use think\Controller;
use app\dal\model\User;

class Analysis extends Controller
{

    private function build_children($id)
    {
        $user = User::get($id);
        $res = [];
        $user_children = User::all([
            "from_wx_id" => $user->wx_id
        ]);
        $res["name"] = $user->name;
        $res["children"] = [];
        foreach ($user_children as $user_child) {
            if ($user_child->name == ''){
                continue;
            }
            array_push($res["children"], $this->build_children($user_child->id));
        }
        return $res;
    }

    public function user_relation()
    {

        return $this->build_children(1548);
//        return $this->build_children(73);
    }
}
