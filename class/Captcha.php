<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.captcha.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use XoopsModules\Wfresource;

//require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/captcha/php-captcha.inc.php';

/**
 * Captcha
 *
 * @author    John
 * @copyright Copyright (c) 2007
 */
class Captcha extends Wfresource\Captcha\PhpCaptcha
{
    public $_font;
    public $_errors;

    /**
     * Captcha::__construct()
     */
    public function __construct()
    {
        $this->_font = [XOOPS_ROOT_PATH . '/modules/wfresource/class/media/VeraMoBd.ttf'];
    }

    /**
     * Captcha::loadFont()
     */
    public function loadFont(array $value = null): void
    {
    }

    /**
     * Captcha::create()
     * @param string $sFilename
     */
    public function create($sFilename = ''): void
    {
        $oVisualCaptcha = new PhpCaptcha($aFonts, 150, 40);
        $oVisualCaptcha->setFileType('png');
        $oVisualCaptcha->useColour(true);
        $oVisualCaptcha->create('visual-captcha.php');
    }

    /**
     * @param      $sUserCode
     * @param bool $bCaseInsensitive
     */
    public function validate($sUserCode, $bCaseInsensitive = true): void
    {
    }
}
