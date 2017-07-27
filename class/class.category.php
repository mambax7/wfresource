<?php
// $Id: class.category.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                                //
// Copyright (c) 2007 Xoops                                         //
// //
// Authors:                                                                 //
// John Neill ( AKA Catzwolf )                                              //
// Raimondas Rimkevicius ( AKA Mekdrop )                                    //
// //
// URL: http:www.xoops.com                                              //
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

wfp_getObjectHandler();

/**
 * wfp_Category
 *
 * @package
 * @author    John
 * @copyright Copyright (c) 2006
 * @access    public
 */
class wfp_Category extends wfp_Object
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('category_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_pid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_mid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('category_title', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('category_description', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_image', XOBJ_DTYPE_TXTBOX, '', false, 150);
        $this->initVar('category_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('category_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('category_published', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('category_imageside', XOBJ_DTYPE_OTHER, 'left', false);
        $this->initVar('category_type', XOBJ_DTYPE_TXTBOX, null, false, 10);
        $this->initVar('category_header', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_footer', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_body', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_folders', XOBJ_DTYPE_ARRAY, null, false, null);
        $this->initVar('category_metatitle', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('category_meta', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
        $this->initVar('category_keywords', XOBJ_DTYPE_TXTAREA, null, false, null, 1);
    }
}

/**
 * wfp_CategoryHandler
 *
 * @package
 * @author    John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @access    public
 */
class wfp_CategoryHandler extends wfp_ObjectHandler
{
    /**
     * wfp_CategoryHandler::wfp_CategoryHandler()
     *
     * @param $db
     */
    public function __construct($db)
    {
        parent::__construct($db, 'wfp_category', 'wfp_Category', 'category_id', 'category_title', 'wfp_category_read', 'wfp_category_write');
    }

    /**
     * wfp_CategoryHandler::getRead_permissions()
     *
     * @param mixed  $obj
     * @param string $type
     * @return
     */
    public function getRead_permissions(&$obj, $type = 'wfp_category_read')
    {
        $_group = wfp_getClass('permissions');
        $_group->setPermissions('category', $type, '', 1);
        $group_array = $_group->getAdmin(@$obj->getVar('category_id'));

        return $group_array;
    }

    /**
     * wfp_SectionHandler::getSave_permissions()
     *
     * @param  mixed  $obj
     * @param  string $type : This is the type of permission to be saved example: Read, submit, moderator permissions
     * @param  mixed  $value
     * @return bool
     */
    public function getSave_permissions(&$obj, $type = 'wfp_category_read', $value = null)
    {
        if (!is_array($value)) {
            return false;
        }
        $_group = wfp_getClass('permissions');
        $_group->setPermissions('category', $type, 'Menu permissions', 1);
        $result = $_group->save($value, @$obj->getVar('category_id'));

        return $result;
    }

    /**
     * wfp_CategoryHandler::getCategoryObj()
     * @return bool
     */
    public function getObj()
    {
        $obj = false;
        if (func_num_args() === 2) {
            $args     = func_get_args();
            $criteria = new CriteriaCompo();
            if ($GLOBALS['xoopsModule']->getVar('mid')) {
                $criteria->add(new Criteria('category_mid', $GLOBALS['xoopsModule']->getVar('mid')));
            }
            // if ($args[0]['category_display'] == 0 OR $args[0]['category_display'] == 1) {
            // $criteria->add ( new Criteria( 'category_display', $args[0]['category_display'] ) );
            // }
            $obj['count'] = $this->getCount($criteria);
            if (!empty($args[0])) {
                $criteria->setSort($args[0]['sort']);
                $criteria->setOrder($args[0]['order']);
                $criteria->setStart($args[0]['start']);
                $criteria->setLimit($args[0]['limit']);
            }
            $obj['list'] = $this->getObjects($criteria, $args[1]);
        }

        return $obj;
    }

    /**
     * wfp_CategoryHandler::getMenuObj()
     * @return array|bool $obj
     */
    public function getMenuObj()
    {
        $criteria = new CriteriaCompo();
        if ($GLOBALS['xoopsModule']->getVar('mid')) {
            $criteria->add(new Criteria('category_mid', $GLOBALS['xoopsModule']->getVar('mid')));
        }
        $obj = $this->getObjects($criteria, false);

        return $obj;
    }

    /**
     * wfp_CategoryHandler::getAllImages()
     * @return bool|string
     */
    public function getAllImages()
    {
        // $db = &wfp_DatabaseFactory::getDatabaseConnection();
        $sql    = 'SELECT DISTINCT category_id, category_image FROM ' . $this->db->prefix('category');
        $result = $this->db->query($sql);
        if (!$result) {
            return false;
        }
        $ret = '';
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            if ($myrow['category_image'] !== '||') {
                $ret[$myrow['category_id']] = htmlspecialchars($myrow['category_image'], ENT_QUOTES);
            }
        }

        return $ret;
    }

    /**
     * wfc_PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count)
    {
        /**
         * bad bad bad!! Need to change this
         */
        global $list_array, $nav, $section_list;
        /**
         */
        $onchange = 'onchange=\'location="admin.category.php?%s="+this.options[this.selectedIndex].value\'';
        $ret      = '<div style="padding-bottom: 16px;">';
        $ret .= '<form>
         <div style="text-align: left; margin-bottom: 12px;">
          <input type="button" name="button" onclick=\'location="admin.category.php?op=edit"\' value="' . _MD_WFP_CREATENEW . '">
          <input type="button" name="button" onclick=\'location="admin.category.php?op=permissions"\' value="' . _MD_WFP_PERMISSIONS . '">
         </div></form>';
        $ret .= '<div>
            <span style="float: right;">' . _AM_WFP_DISPLAYAMOUNT_BOX . wfp_getSelection(wfp_ListArray(), $nav['limit'], 'limit', 1, 0, false, false, sprintf($onchange, 'limit'), 0, false) . '</span>
            </div>';
        $ret      .= '</div><br clear="all">';
        echo $ret;
    }
}
