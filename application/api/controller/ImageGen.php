<?php

namespace app\api\controller;

use think\Controller;
use org\wechat\JSSDK;
use  org\phpqrcode\QRcode;

class ImageGen extends Controller
{
    private $font_size = 18;
    private $width = 500;
    private $height = 800;
    private $line_height = 40;

    public function test()
    {
        $name = "雷帅";
        $n_correct_question = 13;
        $by_pass_domestic = 98;
        $by_pass_global = 93;
        $font_path = dirname(__FILE__) ."/puhui.ttf";
        $qr_code_path = dirname(__FILE__) ."/qrcode/qrcode.png";

        $img = imagecreatefrompng(dirname(__FILE__). "/images/封底.png");
        $black = imagecolorallocate($img, 0,0,0);
        $red = imagecolorallocate($img, 201,0,32);

        // 插入图片
        $achievementImgPath = dirname(__FILE__). "/images/太行精神.png";
        $achievementImg = imagecreatefrompng($achievementImgPath);
        $achievementImgInfo = getimagesize($achievementImgPath);
        $achievementImgWidth = $achievementImgInfo[0];
        $achievementImgHeight = $achievementImgInfo[1];
        imagecopy($img, $achievementImg, 40, 240, 0, 0, $achievementImgWidth, $achievementImgHeight);

        // 写成就
        $text_achievement = "达成成就";
        imagettftext($img, 30, 0, 400, 980, $black, $font_path, $text_achievement);
        $text_achievement = "太行精神";
        imagettftext($img, 50, 0, 400, 1050, $red, $font_path, $text_achievement);

        // 写字
        $text_name = $name."参与了xxx答题活动";
        imagettftext($img, $this->font_size,0, 40, 980,$black, dirname(__FILE__) ."/puhui.ttf", $text_name);
        $text_correct = "答对了        道题目";
        imagettftext($img, $this->font_size,0, 40, 1020,$black, dirname(__FILE__) ."/puhui.ttf", $text_correct);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1020,$red, dirname(__FILE__) ."/puhui.ttf", $n_correct_question);
        $text_bypass_domestic = "超过了        %的国内参与者";
        imagettftext($img, $this->font_size,0, 40, 1060,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_domestic);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1060,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_domestic);
        $text_bypass_global = "超过了        %的全球参与者";
        imagettftext($img, $this->font_size,0, 40, 1100,$black, dirname(__FILE__) ."/puhui.ttf", $text_bypass_global);
        imagettftext($img, 28,0, 40 + $this->font_size * 4, 1100,$red, dirname(__FILE__) ."/puhui.ttf", $by_pass_global);
//        $text_1 = $name."本轮共答对".$n_correct_question."道";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height,$black, dirname(__FILE__) ."/puhui.ttf", $text_1);
//
//        $text_2 = "超过了".$by_pass_domestic."%的国内用户和".$by_pass_global."%的全球用户";
//        imagettftext($img, $this->font_size,0, 20,$this->line_height * 2,$black, dirname(__FILE__) ."/puhui.ttf", $text_2);

        $qr_img = QRcode::png("http://dict.fgb2019.top/index/index/sign_in?share_user=123", false,QR_ECLEVEL_L, 3, 4, false);
        imagecopy($img, $qr_img, 40,1120, 0, 0, 120, 120);
        // 引导扫码
        $text_scan = "长按扫描二维码";
        imagettftext($img, 14,0, 170, 1150,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);
        $text_scan = "参与答题";
        imagettftext($img, 14,0, 170, 1170,$black, dirname(__FILE__) ."/puhui.ttf", $text_scan);


        //返回图片
        imagepng($img);
        imagedestroy($qr_img);
        imagedestroy($achievementImg);
        imagedestroy($img);
    }
}