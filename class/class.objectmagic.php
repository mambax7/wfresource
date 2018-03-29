<?php
/**
 * Name: class.objectmagic.php
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
defined('XOOPS_ROOT_PATH') || die('Restricted access');

wfp_loadLangauge('errors', 'wfresource');

/**
 * xo_ObjectMagicHandler
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2009
 * @access    public
 */
class xo_ObjectMagicHandler extends xo_PersistableObjectHandler
{
    public $handler;
    public $mhandler;

    public $obj_table;
    public $obj_class;
    public $obj_keyname;
    public $obj_idname; //identifierName
    public $obj_groups;
    public $obj_id;
    public $obj_url;

    /**
     * xo_ObjectMagicHandler::__construct()
     */
    public function __construct()
    {
    }

    /**
     * xo_ObjectMagicHandler::loadHandler()
     *
     * @param $handler
     * @param $mhandler
     * @return bool
     */
    public function loadHandler($handler, $mhandler)
    {
        if (!is_object($handler)) {
            trigger_error('Handler: ' . get_class($handler) . ' is not of the required type. Object expected but not given');

            return false;
        }
        /**
         */
        $this->handler  = $handler;
        $this->mhandler = $mhandler;
    }
}
