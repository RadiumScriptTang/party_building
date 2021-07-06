<?php

namespace app\api\controller;

use think\Controller;
use org\wechat\JSSDK;
use  org\phpqrcode\QRcode;
use think\Session;

class ImageGen extends Controller
{
    //
    private $font_size = 24;
    private $width = 500;
    private $height = 800;
    private $line_height = 40;
    private $images = ['两弹一星精神.png', '五四精神.png', '井冈山精神.png', '南泥湾精神.png',
        '延安精神.png', '焦裕禄精神.png', '红船精神.png', '苏区精神.png', '西迁精神.png',
        '长征精神.png', '雷锋精神.png'];

    public function share_image()
    {
        $info = Session::get("render_info");
        Session::set("render_info", null);
        $name = $info["name"];
        $wx_id = $info["wx_id"];
        $n_correct_question = $info["n_correct"];
        $by_pass_domestic = number_format($info["bypass_domestic"],1);
        $by_pass_global = number_format($info["bypass_global"], 1);
        $font_path = dirname(__FILE__) ."/puhui.ttf";
        $qr_code_path = dirname(__FILE__) ."/qrcode/qrcode.png";
        $rank = $info["rank"];

        $img = imagecreatefrompng(dirname(__FILE__). "/images/封底.png");
        $black = imagecolorallocate($img, 0,0,0);
        $red = imagecolorallocate($img, 201,0,32);

        // 插入图片
        $imageName = $this->images[rand() % 11];
        $achievementImgPath = dirname(__FILE__). "/images/".$imageName;
        $achievementImg = imagecreatefrompng($achievementImgPath);
        $achievementImgInfo = getimagesize($achievementImgPath);
        $achievementImgWidth = $achievementImgInfo[0];
        $achievementImgHeight = $achievementImgInfo[1];
        imagecopy($img, $achievementImg, 55, 240, 0, 0, $achievementImgWidth, $achievementImgHeight);

        // 写成就
//        $text_achievement = "达成成就";
//        imagettftext($img, 30, 0, 400, 980, $black, $font_path, $text_achievement);
//        $text_achievement = "太行精神";
//        imagettftext($img, 50, 0, 400, 1050, $red, $font_path, $text_achievement);


        // 写字
        $text_name = "快测评估".$name."的强国知识";
        imagettftext($img, $this->font_size,0, 40, 900,$black, dirname(__FILE__) ."/puhui.ttf", $text_name);
        $text_name = "达到了\"                   \"段位";

        imagettftext($img, $this->font_size,0, 40, 950,$black, dirname(__FILE__) ."/puhui.ttf", $text_name);
        imagettftext($img, $this->font_size,0, 40 + 5*$this->font_size, 950,$red, dirname(__FILE__) ."/puhui.ttf", $rank);

        $text_correct = "答对了       道题目";
        imagettftext($img, $this->font_size,0, 40, 1000,$black, dirname(__FILE__) ."/puhui.ttf", $text_correct);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1000,$red, dirname(__FILE__) ."/puhui.ttf", $n_correct_question);
        $text_bypass_domestic = "超过了           %的国内参与者";
        imagettftext($img, $this->font_size,0, 40, 1050,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_domestic);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1050,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_domestic);
        $text_bypass_global = "超过了           %的全球参与者";
        imagettftext($img, $this->font_size,0, 40, 1100,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_global);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1100,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_global);
//        $text_1 = $name."本轮共答对".$n_correct_question."道";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height,$black, dirname(__FILE__) ."/puhui.ttf", $text_1);
//
//        $text_2 = "超过了".$by_pass_domestic."%的国内用户和".$by_pass_global."%的全球用户";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height * 2,$black, dirname(__FILE__) ."/puhui.ttf", $text_2);

        $qr_img = QRcode::png("http://arena.fgb2019.top/index/index/sign_in?from_wx_id=".$wx_id, false,0, 6, 4, false);

        imagecopyresized($img, $qr_img, 30,1120, 0, 0, 140, 140, imagesx($qr_img), imagesy($qr_img));
        // 引导扫码
        $text_scan = "长按扫描二维码";
        imagettftext($img, 14,0, 190, 1170,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);
        $text_scan = "参与答题";
        imagettftext($img, 14,0, 190, 1190,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);


        //返回图片
        imagepng($img);
        imagedestroy($qr_img);
        imagedestroy($achievementImg);
        imagedestroy($img);
    }

    public function qr_test()
    {
//        $info = Session::get("render_info");
        Session::set("render_info", null);
        $name = "雷佳乐";
        $wx_id = "xxxxxxxxx";
        $n_correct_question = 10;
        $by_pass_domestic = number_format(98.56,1);
        $by_pass_global = number_format(91.22, 1);
        $font_path = dirname(__FILE__) ."/puhui.ttf";
        $qr_code_path = dirname(__FILE__) ."/qrcode/qrcode.png";

        $img = imagecreatefrompng(dirname(__FILE__). "/images/封底.png");
        $black = imagecolorallocate($img, 0,0,0);
        $red = imagecolorallocate($img, 201,0,32);

        // 插入图片
        $imageName = $this->images[rand() % 11];
        $achievementImgPath = dirname(__FILE__). "/images/".$imageName;
        $achievementImg = imagecreatefrompng($achievementImgPath);
        $achievementImgInfo = getimagesize($achievementImgPath);
        $achievementImgWidth = $achievementImgInfo[0];
        $achievementImgHeight = $achievementImgInfo[1];
        imagecopy($img, $achievementImg, 55, 240, 0, 0, $achievementImgWidth, $achievementImgHeight);

        // 写成就
//        $text_achievement = "达成成就";
//        imagettftext($img, 30, 0, 400, 980, $black, $font_path, $text_achievement);
//        $text_achievement = "太行精神";
//        imagettftext($img, 50, 0, 400, 1050, $red, $font_path, $text_achievement);

        // TODO:::
        $rank = "一心一意";

        // 写字
        $text_name = "快测评估".$name."的强国知识";
        imagettftext($img, $this->font_size,0, 40, 900,$black, dirname(__FILE__) ."/puhui.ttf", $text_name);
        $text_name = "达到了\"                   \"段位";

        imagettftext($img, $this->font_size,0, 40, 950,$black, dirname(__FILE__) ."/puhui.ttf", $text_name);
        imagettftext($img, $this->font_size,0, 40 + 5*$this->font_size, 950,$red, dirname(__FILE__) ."/puhui.ttf", $rank);

        $text_correct = "答对了       道题目";
        imagettftext($img, $this->font_size,0, 40, 1000,$black, dirname(__FILE__) ."/puhui.ttf", $text_correct);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1000,$red, dirname(__FILE__) ."/puhui.ttf", $n_correct_question);
        $text_bypass_domestic = "超过了           %的国内参与者";
        imagettftext($img, $this->font_size,0, 40, 1050,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_domestic);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1050,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_domestic);
        $text_bypass_global = "超过了           %的全球参与者";
        imagettftext($img, $this->font_size,0, 40, 1100,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_global);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1100,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_global);
//        $text_1 = $name."本轮共答对".$n_correct_question."道";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height,$black, dirname(__FILE__) ."/puhui.ttf", $text_1);
//
//        $text_2 = "超过了".$by_pass_domestic."%的国内用户和".$by_pass_global."%的全球用户";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height * 2,$black, dirname(__FILE__) ."/puhui.ttf", $text_2);

        $qr_img = QRcode::png("http://arena.fgb2019.top/index/index/sign_in?from_wx_id=".$wx_id, false,0, 6, 4, false);

        imagecopyresized($img, $qr_img, 30,1120, 0, 0, 140, 140, imagesx($qr_img), imagesy($qr_img));
        // 引导扫码
        $text_scan = "长按扫描二维码";
        imagettftext($img, 14,0, 190, 1170,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);
        $text_scan = "参与答题";
        imagettftext($img, 14,0, 190, 1190,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);


        //返回图片
        imagepng($img);
        imagedestroy($qr_img);
        imagedestroy($achievementImg);
        imagedestroy($img);
    }
}