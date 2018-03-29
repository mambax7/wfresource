<?php
/**
 * CAPTCHA form element
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  XOOPS Project (https://xoops.org)
 * @license    http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package    kernel
 * @subpackage form
 * @since      2.0.0
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @author     Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access.');

/**
 * Usage of XoopsFormCaptcha
 *
 * For form creation:
 * Add form element where proper: <code>$xoopsform->addElement(new XoopsFormCaptcha($caption, $name, $skipmember, $configs));</code>
 *
 * For verification:
 * <code>
 *       xoops_load("captcha");
 *       $xoopsCaptcha = XoopsCaptcha::getInstance();
 *       if (! $xoopsCaptcha->verify() ) {
 *           echo $xoopsCaptcha->getMessage();
 *           ...
 *       }
 * </code>
 */
class XoopsFormCaptcha extends XoopsFormElement
{
    public $captchaHandler;

    /**
     *
     * @param string  $caption    Caption of the form element, default value is defined in captcha/language/
     * @param string  $name       Name for the input box
     * @param boolean $skipmember Skip CAPTCHA check for members
     * @param array   $configs
     */
    public function __construct($caption = '', $name = 'xoopscaptcha', $skipmember = true, $configs = [])
    {
        parent::__construct();
        xoops_load('captcha');

        $this->captchaHandler  = XoopsCaptcha::getInstance();
        $configs['name']       = $name;
        $configs['skipmember'] = $skipmember;
        $this->captchaHandler->setConfigs($configs);
        if (!$this->captchaHandler->isActive()) {
            $this->setHidden();
        } else {
            $caption = !empty($caption) ? $caption : $this->captchaHandler->getCaption();
            $this->setCaption($caption);
            $this->setName($name);
        }
    }

    /**
     * @param $name
     * @param $val
     * @return mixed
     */
    public function setConfig($name, $val)
    {
        return $this->captchaHandler->setConfig($name, $val);
    }

    /**
     * @return mixed|string
     */
    public function render()
    {
        // if (!$this->isHidden()) {
        return $this->captchaHandler->render();
        // }
    }
}
