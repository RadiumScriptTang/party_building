<?php

namespace app\api\controller;

use think\Controller;
use org\wechat\JSSDK;

class Wx extends Controller
{
    public function access_token()
    {
        $url = input("url");
        $jssdk = new JSSDK("wx325c2d1d888031de", "d8e828fac48fa928772e85760575b24e");
        $signPackage = $jssdk->GetSignPackage($url);
        return [
            "appId" => $signPackage["appId"],
            "nonceStr" => $signPackage["nonceStr"],
            "timestamp" => $signPackage["timestamp"],
            "signature" => $signPackage["signature"]
        ];
    }

    public function check_wechat()
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "RadiumToken";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}