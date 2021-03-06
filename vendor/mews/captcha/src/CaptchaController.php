<?php

namespace Mews\Captcha;

use Illuminate\Routing\Controller;

/**
 * Class CaptchaController
 * @package Mews\Captcha
 */
class CaptchaController extends Controller
{

    /**
     * get CAPTCHA
     *
     * @param \Mews\Captcha\Captcha $captcha
     * @param string $config
     * @return \Intervention\Image\ImageManager->response
     */
    public function getCaptcha(Captcha $captcha, $config = 'default')
    {
        /* 清除图片缓存 */
        ob_clean();
        
        return $captcha->create($config);
    }

}
