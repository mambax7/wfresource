<?php
/**
 * Name: class.dummy.php
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

wfp_getObjectHandler();

/**
 * wfp_Dummy
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_Dummy extends wfp_Object
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
}

/**
 * wfp_DummyHandler
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class wfp_DummyHandler extends wfp_ObjectHandler
{
    /**
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db, '', 'wfp_Dummy');
    }
}
