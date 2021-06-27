<?php
namespace app\index\controller;
use think\Controller;
use app\dal\model\User;

class Test extends Controller
{
    public function index()
    {
        return "Hello!   !!!!!";

    }

    public function test()
    {
        return User::get(1);
    }

    public function counter()
    {
        return $this->fetch();
    }

}
