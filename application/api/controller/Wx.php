<?php

namespace app\api\controller;

use think\Controller;
use org\wechat\JSSDK;

class Wx extends Controller
{
    public function access_token()
    {
        $url = input("url");
        $jssdk = new JSSDK("wxf994642134c1b2ed", "4e35ca6c15d7fc6922805630970de1b1");
        $signPackage = $jssdk->GetSignPackage($url);
        return [
            "appId" => $signPackage["appId"],
            "nonceStr" => $signPackage["nonceStr"],
            "timestamp" => $signPackage["timestamp"],
            "signature" => $signPackage["signature"],
            "openid" => $signPackage["openid"]
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