<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

/**
 * Name: class.dummy.php
 * Description:
 *
 * @Module     :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 */

//wfp_getObjectHandler();

/**
 * Dummy
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */

/**
 * DummyHandler
 *
 * @author    John
 * @copyright Copyright (c) 2009
 */
class WfpDummyHandler extends WfpObjectHandler
{
    /**
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, '', WfpDummy::class);
    }
}
