<?php

namespace app\api\controller;

use think\Controller;
use org\wechat\JSSDK;

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

        $img = imagecreatetruecolor($this->width, $this->height);
        $red = imagecolorallocate($img, 246, 238, 231);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $red);

        // 写字
        $text_1 = $name."本轮共答对".$n_correct_question."道";
        imagettftext($img, $this->font_size,0, 20,$this->line_height,$black, dirname(__FILE__) ."/puhui.ttf", $text_1);

        $text_2 = "超过了".$by_pass_domestic."%的国内用户和".$by_pass_global."%的全球用户";
        imagettftext($img, $this->font_size,0, 20,$this->line_height * 2,$black, dirname(__FILE__) ."/puhui.ttf", $text_2);
        imagepng($img);

        imagedestroy($img);
    }
}