<?php
/**
 * Name: class.captcha.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

require XOOPS_ROOT_PATH . '/modules/wfresource/class/captcha/php-captcha.inc.php';

/**
 * wfp_captcha
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2007
 * @access    public
 */
class wfp_captcha extends PhpCaptcha
{
    public $_font;
    public $_errors;

    /**
     * wfp_captcha::__construct()
     *
     */
    public function __construct()
    {
        $this->_font = [XOOPS_ROOT_PATH . '/modules/wfresource/class/media/VeraMoBd.ttf'];
    }

    /**
     * wfp_captcha::loadFont()
     *
     * @param array $value
     */
    public function loadFont(array $value = null)
    {
    }

    /**
     * wfp_captcha::create()
     * @param string $sFilename
     * @return bool|void
     */
    public function create($sFilename = '')
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
    public function validate($sUserCode, $bCaseInsensitive = true)
    {
    }
}
