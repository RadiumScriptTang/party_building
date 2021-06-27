<?php
namespace app\index\controller;
use think\Controller;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        if (!Session::get("user_id") || Session::get("user_id") == "admin"){
            $this->redirect("/index/index/sign_in");
        }
        return $this->fetch();
    }
    public function sign_in()
    {
        return $this->fetch();
    }

    public function admin_sign_in()
    {
        return $this->fetch();
    }
    public function admin()
    {
        if (Session::get("user_id") != "admin")
        {
            $this->redirect("/index/index/admin_sign_in");
        }
        return $this->fetch();
    }

    public function answer_question()
    {
        return $this->fetch();
    }

    public function test()
    {
        return $this->fetch();
    }

    public function ttest()
    {
        return $this->fetch();
    }

    public function show_result()
    {
        return $this->fetch();
    }
}
