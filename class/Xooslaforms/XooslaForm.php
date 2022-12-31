<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Xooslaforms;

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

use XoopsModules\Wfresource\Xooslaforms;

/**
 * XooslaForm
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class XooslaForm extends \XoopsForm
{
    public $_tabs;

    /**
     * XooslaForm::doTabs()
     */
    public function doTabs(): void
    {
        $this->_tabs = new Xooslaforms\XooslaFormTabs(false);
    }
}
