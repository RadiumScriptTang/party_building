<?php

namespace app\index\controller;

use think\Controller;

class Analysis extends Controller
{
    public function user_propagation()
    {
        return $this->fetch();
    }
}