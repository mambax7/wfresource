<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

// ------------------------------------------------------------------------ //
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects                                                 //
// Copyright (c) 2007 WF-Channel                                            //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// //
// URL: https://catzwolf.x10hosting.com/                                     //
// Project: WF-Projects                                                     //
// -------------------------------------------------------------------------//

/**
 * This class is copyright Xoops.com and must remain so.
 */

use XoopsDatabaseFactory;
use XoopsGroupPermForm;

/**
 * PermissionsHandler
 *
 * @author    Catzwolf
 * @copyright Copyright (c) 2005
 */
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

//xoops_load( 'XoopsGroupPermForm' );

/**
 * Class Permissions
 */
class Permissions extends XoopsGroupPermForm
{
    public $db;
    public $db_table;
    public $mod_id = 0;
    public $perm_name;
    public $perm_descript;
    public $_mod_id;
    public $_perm_name;
    public $_perm_descript;

    /**
     * Permissions::__construct()
     */
    public function __construct()
    {
        // null value
    }

    /**
     * Permissions::setPermissions()
     *
     * @param string $table
     * @param string $perm_name
     * @param string $perm_descript
     * @param mixed  $mod_id
     */
    public function setPermissions($table, $perm_name, $perm_descript, $mod_id): void
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
     * Permissions::render()
     *
     * @param array $arr
     */
    public function render($arr = []): void
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
            \trigger_error($error);
        }

        $form_info = new XoopsGroupPermForm('', $this->_mod_id, $this->_perm_name, $this->_perm_descript);
        if ($this->db->getRowsNum($result)) {
            while (false !== ($row_arr = $this->db->fetchArray($result))) {
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
     * Permissions::save()
     *
     * @param mixed $item_id
     * @return bool
     */
    public function save(array $groups = null, $item_id = 0)
    {
        $item_id = (int)$item_id;
        if (!\is_array($groups) || !\count($groups) || 0 === $item_id) {
            return false;
        }

        /**
         * Save the new permissions
         */
        $grouppermHandler = \xoops_getHandler('groupperm'); // wfp_getHandler('groupperm');
        if (\is_object($grouppermHandler) && null !== $grouppermHandler) {
            /**
             * First, if the permissions are already there, delete them
             */
            $grouppermHandler->deleteByModule($this->_mod_id, $this->_perm_name, $item_id);
            foreach ($groups as $group_id) {
                if (!$grouppermHandler->addRight($this->_perm_name, $item_id, $group_id, $this->_mod_id)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Permissions::get()
     *
     * @param mixed $item_id
     * @return bool
     */
    public function get($item_id)
    {
        global $xoopsUser;

        $item_id          = (string)((int)$item_id);
        $groups           = \is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $grouppermHandler = \xoops_getHandler('groupperm'); //wfp_getHandler('groupperm');
        if ($groups && \is_object($grouppermHandler)) {
            $ret = $grouppermHandler->checkRight($this->_perm_name, $item_id, $_groups, $this->_mod_id);

            return $ret;
        }

        return false;
    }

    /**
     * Permissions::getAdmin()
     *
     * @param mixed $item_id
     * @param mixed $isNew
     */
    public function getAdmin($item_id, $isNew = null): array
    {
        $item_id          = (int)$item_id;
        $grouppermHandler = \xoops_getHandler('groupperm'); //wfp_getHandler('groupperm');
        $groups           = $grouppermHandler->getGroupIds($this->_perm_name, $item_id, $this->_mod_id);
        if (!\count($groups) && true === $isNew) {
            $groups = [0 => 1, 1 => 2];
        }

        return $groups;
    }

    /**
     * Permissions::doDelete()
     *
     * @param mixed $item_id
     * @return bool
     */
    public function doDelete($item_id)
    {
        global $xoopsUser;

        $item_id          = (int)$item_id;
        $groups           = \is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $grouppermHandler = \xoops_getHandler('groupperm'); //wfp_getHandler('groupperm');
        if ($groups && \is_object($grouppermHandler)) {
            $grouppermHandler->deleteByModule($this->_mod_id, $this->_perm_name, $item_id);
        }

        return false;
    }
}
