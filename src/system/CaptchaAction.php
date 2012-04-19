<?php

/**
 * QCaptchaAction class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CCaptchaAction renders a CAPTCHA image.
 *
 * CCaptchaAction is used together with {@link CCaptcha} and {@link CCaptchaValidator}
 * to provide the {@link http://en.wikipedia.org/wiki/Captcha CAPTCHA} feature.
 *
 * You must configure properties of CCaptchaAction to customize the appearance of
 * the generated image.
 *
 * Note, CCaptchaAction requires PHP GD2 extension.
 *
 * Using CAPTCHA involves the following steps:
 * <ol>
 * <li>Override {@link CController::actions()} and register an action of class CCaptchaAction with ID 'captcha'.</li>
 * <li>In the form model, declare an attribute to store user-entered verification code, and declare the attribute
 * to be validated by the 'captcha' validator.</li>
 * <li>In the controller view, insert a {@link CCaptcha} widget in the form.</li>
 * </ol>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CCaptchaAction.php 2900 2011-01-21 08:55:27Z keyboard.idol@gmail.com $
 * @package system.web.widgets.captcha
 * @since 1.0
 */
class CaptchaAction extends Yaf_Action_Abstract {
	public $backColor = null;
	
	public $fontColor = null;
    
    public $fixedVerifyCode = null;
    
    protected $captchaPrefix = "vsenho.captcha.";
	
	/**
	 * Runs the action.
	 */
	public function execute() {
		$this->getVerifyCode(true);
		// we add a random 'v' parameter so that FireFox can refresh the image
		// when src attribute of image tag is changed
		//echo $this->getController()->createUrl($this->getId(),array('v' => uniqid()));
		$this->renderImage($this->getVerifyCode());
		
		exit;
	}

	/**
	 * Gets the verification code.
	 * @param boolean $regenerate whether the verification code should be regenerated.
	 * @return string the verification code.
	 */
	public function getVerifyCode($regenerate=false) {
		if ($this->fixedVerifyCode !== null) {
			return $this->fixedVerifyCode;
        }
		$session = $this->getController()->session;
		$name = $this->getSessionKey();
		if($session[$name] === null || $regenerate) {
			$session[$name] = $this->generateVerifyCode();
			$session[$name . 'count'] = 1;
		}
		
		return $session[$name];
	}
	
	protected function getSessionKey() {
	    if(!isset($this->sessionKey)) {
	        $unique_id = $this->getController()->getUniqueId();
	        $controller_id = $this->getController()->getId();
	        $this->sessionKey = "{$this->captchaPrefix}{$unique_id}_{$controller_id}";
	    }
	    
	    return $this->sessionKey;
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

	/**
	 * Renders the CAPTCHA image based on the code.
	 * @param string $code the verification code
	 * @return string image content
	 */
	protected function renderImage($code)
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
		for ($i=0, $j=strlen($code); $i<$j;$i++) {
			imagettftext($image, 30, rand(0,20)-rand(0,25), 5+$i*30, rand(30,35), $fontColor, $fontstyle, $code[$i]);
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

}