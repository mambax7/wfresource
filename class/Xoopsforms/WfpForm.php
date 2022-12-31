<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xoopsforms;

/**
 * Name: form.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

use XoopsForm;
use XoopsModules\Wfresource;
use XoopsModules\Wfresource\Xoopsforms;

require_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';

/**
 * WfpForm
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class WfpForm extends XoopsForm
{
    public $_tabs;

    /**
     * WfpForm::doTabs()
     */
    public function doTabs(): void
    {
        //        require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtabs.php';
        $this->_tabs = new Xoopsforms\XoopsFormTab(false);
    }
}
