<?php

// ------------------------------------------------------------------------ //
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects                                                 //
// Copyright (c) 2007 WF-Channel                                            //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// //
// URL: http://catzwolf.x10hosting.com/                                     //
// Project: WF-Projects                                                     //
// -------------------------------------------------------------------------//
/**
 * This class is copyright Xoops.com and must remain so.
 */
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

/**
 * wfp_PermissionsHandler
 *
 * @package
 * @author    Catzwolf
 * @copyright Copyright (c) 2005
 * @access    public
 */
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

//xoops_load( 'XoopsGroupPermForm' );

/**
 * Class wfp_Permissions
 */
class wfp_Permissions extends XoopsGroupPermForm
{
    public $db;
    public $db_table;
    public $mod_id = 0;
    public $perm_name;
    public $perm_descript;

    /**
     * wfp_Permissions::wfp_Permissions()
     *
     */
    public function __construct()
    {
        // null value
    }

    /**
     * wfp_Permissions::setPermissions()
     *
     * @param string $table
     * @param string $perm_name
     * @param string $perm_descript
     * @param mixed  $mod_id
     */
    public function setPermissions($table = '', $perm_name = '', $perm_descript = '', $mod_id)
    {
        if (!empty($table)) {
            $this->db       = XoopsDatabaseFactory::getDatabaseConnection();
            $this->db_table = $this->db->prefix($table);
        }
        $this->_mod_id        = (int)$mod_id;
        $this->_perm_name     = (string)$perm_name;
        $this->_perm_descript = (string)$perm_descript;
    }

    /**
     * wfp_Permissions::wfp_Permissions_render()
     *
     * @param  array $arr
     * @return string|void
     */
    public function render($arr = [])
    {
        $ret           = '';
        $perm_descript = null;
        if ($this->_perm_descript) {
            $perm_descript = $this->_perm_descript;
        }
        $sql = "SELECT {$arr['cid']}";
        if (!empty($arr['pid'])) {
            $sql = ", {$arr['pid']}";
        }
        $sql .= ", {$arr['title']} FROM " . $this->db_table;

        if (!empty($arr['where'])) {
            $sql .= " WHERE {$arr['where']}=" . $this->_mod_id;
        }

        if (!empty($arr['order'])) {
            $sql .= " ORDER BY {$arr['order']}";
        }

        if (!$result = $this->db->query($sql)) {
            $error = $this->db->error() . ' : ' . $this->db->errno();
            trigger_error($error);
        }

        $form_info = new XoopsGroupPermForm('', $this->_mod_id, $this->_perm_name, $this->_perm_descript);
        if ($this->db->getRowsNum($result)) {
            while (false !== ($row_arr = $this->db->fetcharray($result))) {
                if (!empty($arr['pid'])) {
                    $form_info->addItem($row_arr[$arr['cid']], $row_arr[$arr['title']], $row_arr[$arr['pid']]);
                } else {
                    $form_info->addItem($row_arr[$arr['cid']], $row_arr[$arr['title']], 0);
                }
            }
            $ret = $form_info->render();
        }
        unset($form_info);
        echo $ret;
    }

    /**
     * wfp_Permissions::save()
     *
     * @param  array $groups
     * @param  mixed $item_id
     * @return bool
     */
    public function save(array $groups = null, $item_id = 0)
    {
        $item_id = (int)$item_id;
        if (!is_array($groups) || !count($groups) || 0 === $item_id) {
            return false;
        }

        /**
         * Save the new permissions
         */
        $gpermHandler = wfp_getHandler('groupperm');
        if (is_object($gpermHandler) && !empty($gpermHandler)) {
            /**
             * First, if the permissions are already there, delete them
             */
            $gpermHandler->deleteByModule($this->_mod_id, $this->_perm_name, $item_id);
            foreach ($groups as $group_id) {
                if (!$gpermHandler->addRight($this->_perm_name, $item_id, $group_id, $this->_mod_id)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * wfp_Permissions::get()
     *
     * @param  mixed $item_id
     * @return bool
     */
    public function get($item_id)
    {
        global $xoopsUser;

        $item_id      = (string)((int)$item_id);
        $groups       = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $gpermHandler = wfp_getHandler('groupperm');
        if ($groups && is_object($gpermHandler)) {
            $ret = $gpermHandler->checkRight($this->_perm_name, $item_id, $_groups, $this->_mod_id);

            return $ret;
        }

        return false;
    }

    /**
     * wfp_Permissions::getAdmin()
     *
     * @param  mixed $item_id
     * @param  mixed $isNew
     * @return array
     */
    public function getAdmin($item_id, $isNew = null)
    {
        $item_id      = (int)$item_id;
        $gpermHandler = wfp_getHandler('groupperm');
        $groups       = $gpermHandler->getGroupIds($this->_perm_name, $item_id, $this->_mod_id);
        if (!count($groups) && true === $isNew) {
            $groups = [0 => 1, 1 => 2];
        }

        return $groups;
    }

    /**
     * wfp_Permissions::doDelete()
     *
     * @param  mixed $item_id
     * @return bool
     */
    public function doDelete($item_id)
    {
        global $xoopsUser;

        $item_id      = (int)$item_id;
        $groups       = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $gpermHandler = wfp_getHandler('groupperm');
        if ($groups && is_object($gpermHandler)) {
            $gpermHandler->deleteByModule($this->_mod_id, $this->_perm_name, $item_id);
        }

        return false;
    }
}
