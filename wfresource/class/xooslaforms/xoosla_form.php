<?php
/**
 * Name: form.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 * @version    : $Id: form.php 0000 25/03/2009 12:59:34:000 Catzwolf $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * wfp_Form
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @version   $Id$
 * @access    public
 */
class XooslaForm extends XoopsForm
{
    public $_tabs;

    /**
     * wfp_Form::doTabs()
     *
     * @return
     */
    public function doTabs()
    {
        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xooslaforms/xoosla_formtabs.php';
        $this->_tabs = new XooslaFormTabs(false);
    }
}
