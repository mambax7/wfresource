<?php declare(strict_types=1);

namespace XoopsModules\Wfresource;

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

use XoopsModules\Wfresource;

//wfp_getObjectHandler();

/**
 * CategoryHandler
 *
 * @author    John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 */
class CategoryHandler extends Wfresource\WfpObjectHandler
{
    /**
     * CategoryHandler::CategoryHandler()
     *
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'wfp_category', Category::class, 'category_id', 'category_title', 'wfp_category_read', 'wfp_category_write');
    }

    /**
     * CategoryHandler::getRead_permissions()
     *
     * @param mixed  $obj
     * @param string $type
     * @return array|int[]
     */
    public function getRead_permissions($obj, $type = 'wfp_category_read')
    {
        $_group = new Permissions(); //wfp_getClass('permissions');
        $_group->setPermissions('category', $type, '', 1);
        $group_array = $_group->getAdmin(@$obj->getVar('category_id'));

        return $group_array;
    }

    /**
     * wfp_SectionHandler::getSave_permissions()
     *
     * @param mixed  $obj
     * @param string $type : This is the type of permission to be saved example: Read, submit, moderator permissions
     * @param mixed  $value
     * @return bool
     */
    public function getSave_permissions($obj, $type = 'wfp_category_read', $value = null)
    {
        if (!\is_array($value)) {
            return false;
        }
        $_group = new Permissions(); //wfp_getClass('permissions');
        $_group->setPermissions('category', $type, 'Menu permissions', 1);
        $result = $_group->save($value, @$obj->getVar('category_id'));

        return $result;
    }

    /**
     * CategoryHandler::getCategoryObj()
     * @return bool
     */
    public function getObj(...$args)
    {
        $obj = false;
        if (2 === \func_num_args()) {
//            $args     = \func_get_args();
            $criteria = new \CriteriaCompo();
            if ($GLOBALS['xoopsModule']->getVar('mid')) {
                $criteria->add(new \Criteria('category_mid', $GLOBALS['xoopsModule']->getVar('mid')));
            }
            // if ($args[0]['category_display'] == 0 OR $args[0]['category_display'] == 1) {
            // $criteria->add ( new \Criteria( 'category_display', $args[0]['category_display'] ) );
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
     * CategoryHandler::getMenuObj()
     * @return array|bool
     */
    public function getMenuObj()
    {
        $criteria = new \CriteriaCompo();
        if ($GLOBALS['xoopsModule']->getVar('mid')) {
            $criteria->add(new \Criteria('category_mid', $GLOBALS['xoopsModule']->getVar('mid')));
        }
        $obj = $this->getObjects($criteria, false);

        return $obj;
    }

    /**
     * CategoryHandler::getAllImages()
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
            if ('||' !== $myrow['category_image']) {
                $ret[$myrow['category_id']] = \htmlspecialchars($myrow['category_image'], \ENT_QUOTES);
            }
        }

        return $ret;
    }

    /**
     * PageHandler::headingHtml()
     *
     * @param $value
     * @param $total_count
     */
    public function headingHtml($value, $total_count): void
    {
        /**
         * bad bad bad!! Need to change this
         */ global $list_array, $nav, $section_list;

        $onchange = 'onchange=\'location="admin.category.php?%s="+this.options[this.selectedIndex].value\'';
        $ret      = '<div style="padding-bottom: 16px;">';
        $ret      .= '<form>
         <div style="text-align: left; margin-bottom: 12px;">
          <input type="button" name="button" onclick=\'location="admin.category.php?op=edit"\' value="' . _MD_WFP_CREATENEW . '">
          <input type="button" name="button" onclick=\'location="admin.category.php?op=permissions"\' value="' . _MD_WFP_PERMISSIONS . '">
         </div></form>';
        $ret      .= '<div>
            <span style="float: right;">' . \_AM_WFP_DISPLAYAMOUNT_BOX . Utility::getSelection(Utility::listArray(), $nav['limit'], 'limit', 1, 0, false, false, \sprintf($onchange, 'limit'), 0, false) . '</span>
            </div>';
        $ret      .= '</div><br clear="all">';
        echo $ret;
    }
}
