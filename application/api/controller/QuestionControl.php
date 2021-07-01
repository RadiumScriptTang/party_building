<?php

namespace app\api\controller;

use app\dal\model\User;
use app\dal\model\Question;
use think\Controller;
use think\Session;


class QuestionControl extends Controller
{
    public function admin_sign_out()
    {
        Session::set("user_id", "admin");
    }
    public function admin_sign_in()
    {
        $inputs = input();
        $token = $inputs["token"];
        if ($token == "NNF"){
            Session::set("user_id", "admin");
        }
        $this->redirect("/index/index/admin");
    }
    public function set_level()
    {
        $user_id = Session::get("user_id");
        if ($user_id != "admin"){
            return [
                "status" => 1,
                "msg" => "invalid"
            ];
        }
        $inputs = input();
        $question_id = $inputs["id"];
        $question = Question::get($question_id);
        $question->save($inputs);
        return [
            "status" => 0,
            "msg" => "OK"
        ];

    }

    public function get_unleveled_question($id)
    {
        $user_id = Session::get("user_id");
        if ($user_id != "admin"){
            return [
                "status" => 1,
                "msg" => "invalid"
            ];
        }
        $question = Question::get([
            "id" => $id
        ]);
        if ($question){
            return [
                "status" => 0,
                "msg" => "OK",
                "data" => $question
            ];
        }
        return [
            "status" => 1,
            "msg" => "All questions are labeled"
        ];
    }
}