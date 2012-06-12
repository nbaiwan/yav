<?php
/**
 * 文件说明
 * @version $Id: filename May 10, 2012
 * @author Jacky Zhang <zhangbaolin@qvod.com>
 * @copyright 深圳市快播科技有限公司
 */

class Captcha extends Component {
    public $backColor = null;

    public $fontColor = null;

    public $fixedVerifyCode = null;

    protected $captchaPrefix = "kuaibo.pay.captcha.";

    protected $sessionKey = "kuaibo.pay.captcha";
    
    public static $__instance = null;

    /**
     * Gets the verification code.
     * @param boolean $regenerate whether the verification code should be regenerated.
     * @return string the verification code.
     */
    public function getVerifyCode($regenerate=false) {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }
        $session = $this->session;
        if($session[$this->sessionKey] === null || $regenerate) {
            $session[$this->sessionKey] = $this->generateVerifyCode();
            $session[$this->sessionKey . 'count'] = 1;
        }

        return $session[$this->sessionKey];
    }

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    protected function generateVerifyCode() {
        $length = 4;

        $letters = 'bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ23456789';
        $vowels = 'aeuAEU';
        $code = '';
        for($i = 0; $i < $length; ++$i)
        {
            if($i % 2 && mt_rand(0,10) > 2 || !($i % 2) && mt_rand(0,10) > 9)
                $code.=$vowels[mt_rand(0,5)];
            else
                $code.=$letters[mt_rand(0,48)];
        }

        return $code;
    }
    
    public function validateCode($captchaCode) {
        $session = $this->session;
        if (strtolower($session[$this->sessionKey]) == strtolower($captchaCode)) {
            return true;
        } else {
            $session[$this->sessionKey . 'count'] += 1;
            if ($session[$this->sessionKey . 'count'] > 3) {
                $this->getVerifyCode(true);
            }
            return false;
        }
    }

    /**
     * Renders the CAPTCHA image based on the code.
     * @param string $code the verification code
     * @return string image content
     */
    public function renderImage($captchaCode)
    {
        $image = @imagecreate(130,45)or die("Cannot Initialize new GD image stream");

        if ($this->backColor !==null && !is_array($this->backColor)) {
            $this->backColor = array($this->backColor);
        }
        if (count($this->backColor) > 1) {
            $backcolor = array_rand($this->backColor);
        } else {
            $backcolor = $this->backColor ? $this->backColor[0] : rand(50,200) * 255 * 255 + rand(0,155) * 255 + rand(0,155);
        }
        $bg_red = ($backcolor / 256 / 256) % 256;
        $bg_green = ($backcolor / 256) % 256;
        $bg_blue = $backcolor % 256;

        $background = imagecolorallocate($image, $bg_red, $bg_green, $bg_blue);
        //第一次对imagecolorallocate()的调用会给基于调色板的图像填充背景色

        if ($this->fontColor !== null && !is_array($this->fontColor)) {
            $this->fontColor = array($this->fontColor);
        }
        if (count($this->fontColor) > 1) {
            $fontcolor = array_rand($this->fontColor);
        } else {
            $fontcolor = $this->fontColor ? $this->fontColor[0] : 0XFFFFFF;
        }
        $font_red = ($fontcolor / 256 / 256) % 256;
        $font_green = ($fontcolor / 256) % 256;
        $font_blue = $fontcolor % 256;

        $fontColor = imageColorAllocate($image, $font_red, $font_green, $font_blue);  //字体颜色
        $fontstyle = APP_PATH . '/fonts/arial.ttf';
        //字体样式,这个可以从C:\windows\Fonts\文件夹下找到，我把它放到Protected/fonts目录下，这里可以替换其他的字体样式

        //产生随机字符
        for ($i=0, $j=strlen($captchaCode); $i<$j;$i++) {
            imagettftext($image, 30, rand(0,20)-rand(0,25), 5+$i*30, rand(30,35), $fontColor, $fontstyle, $captchaCode[$i]);
        }

        //用户和用户输入验证码做比较

        //干扰线
        for ($i=0; $i<8; $i++) {
            $lineColor = imagecolorallocate($image,rand(0,255),rand(0,255),rand(0,255));
            imageline($image,rand(0,130),0,rand(0,130),145,$lineColor);
        }

        //干扰点
        for ($i=0;$i<250;$i++) {
            imagesetpixel($image, rand(0,130), rand(0,145), $fontColor);
        }

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }
    
    public static function inst() {
        if (self::$__instance == null) {
            self::$__instance = new Captcha();
        }
        
        return self::$__instance;
    }

}