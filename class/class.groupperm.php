<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || die('You do not have permission to access this file!');

/**
 * XOOPS group permission handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS group permission class objects.
 * This class is an abstract class to be implemented by child group permission classes.
 *
 * @see       XoopsGroupPerm
 * @author    Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 */
class wfp_GroupPermHandler extends XoopsGroupPermHandler
{
    /**
     * Store a {@link XoopsGroupPerm}
     *
     * @param XoopsObject|\XoopsGroupPerm $perm a XoopsGroupPerm object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(\XoopsObject $perm)
    {
        if ('xoopsgroupperm' !== strtolower(get_class($perm))) {
            return false;
        }
        if (!$perm->isDirty()) {
            return true;
        }
        if (!$perm->cleanVars()) {
            return false;
        }
        foreach ($perm->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($perm->isNew()) {
            $gperm_id = $this->db->genId('group_permission_gperm_id_seq');
            $sql      = sprintf('INSERT INTO `%s` (gperm_id, gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, %u, %u, %s)', $this->db->prefix('group_permission'), $gperm_id, $gperm_groupid, $gperm_itemid, $gperm_modid, $this->db->quoteString($gperm_name));
        } else {
            $sql = sprintf('UPDATE `%s` SET gperm_groupid = %u, gperm_itemid = %u, gperm_modid = %u WHERE gperm_id = %u', $this->db->prefix('group_permission'), $gperm_groupid, $gperm_itemid, $gperm_modid, $gperm_id);
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        if (empty($gperm_id)) {
            $gperm_id = $this->db->getInsertId();
        }
        $perm->assignVar('gperm_id', $gperm_id);

        return true;
    }

    /**
     * Delete a {@link XoopsGroupPerm}
     *
     * @param XoopsObject|\XoopsGroupPerm $perm a XoopsGroupPerm object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(\XoopsObject $perm)
    {
        if ('xoopsgroupperm' !== strtolower(get_class($perm))) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE gperm_id = %u', $this->db->prefix('group_permission'), $perm->getVar('gperm_id'));
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Delete all permissions by a certain criteria
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement}
     * @return bool            TRUE on success
     */
    public function deleteAll(CriteriaElement $criteria = null)
    {
        $sql = sprintf('DELETE FROM `%s`', $this->db->prefix('group_permission'));
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
