<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

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
 * @license    GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since      2.0.0
 * @author     Kazumi Ono <onokazu@xoops.org>
 * @author     Taiwen Jiang <phppp@users.sourceforge.net>
 */

use XoopsModules\Wfresource\Captcha;

/**
 * Usage of XoopsFormCaptcha
 *
 * For form creation:
 * Add form element where proper: <code>$xoopsform->addElement(new \XoopsFormCaptcha($caption, $name, $skipmember, $configs));</code>
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
     * @param string $caption    Caption of the form element, default value is defined in captcha/language/
     * @param string $name       Name for the input box
     * @param bool   $skipmember Skip CAPTCHA check for members
     * @param array  $configs
     */
    public function __construct($caption = '', $name = 'xoopscaptcha', $skipmember = true, $configs = [])
    {
        parent::__construct();
        \xoops_load('captcha');

        $this->captchaHandler  = \XoopsCaptcha::getInstance();
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
     * @return bool
     */
    public function setConfig($name, $val)
    {
        return $this->captchaHandler->setConfig($name, $val);
    }

    /**
     * @return string
     */
    public function render()
    {
        // if (!$this->isHidden()) {
        return $this->captchaHandler->render();
        // }
    }
}
