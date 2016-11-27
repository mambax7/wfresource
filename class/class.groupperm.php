<?php
// $Id: class.groupperm.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System                      //
// Copyright (c) 2000 XOOPS.org                           //
// <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// //
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// //
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// //
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

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
     * @param XoopsObject|XoopsGroupPerm $perm a XoopsGroupPerm object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $perm)
    {
        if (strtolower(get_class($perm)) !== 'xoopsgroupperm') {
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
            $sql      = sprintf('INSERT INTO %s (gperm_id, gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, %u, %u, %s)', $this->db->prefix('group_permission'), $gperm_id, $gperm_groupid, $gperm_itemid, $gperm_modid,
                                $this->db->quoteString($gperm_name));
        } else {
            $sql = sprintf('UPDATE %s SET gperm_groupid = %u, gperm_itemid = %u, gperm_modid = %u WHERE gperm_id = %u', $this->db->prefix('group_permission'), $gperm_groupid, $gperm_itemid, $gperm_modid, $gperm_id);
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
     * @param XoopsObject|XoopsGroupPerm $perm a XoopsGroupPerm object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $perm)
    {
        if (strtolower(get_class($perm)) !== 'xoopsgroupperm') {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE gperm_id = %u', $this->db->prefix('group_permission'), $perm->getVar('gperm_id'));
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
        $sql = sprintf('DELETE FROM %s', $this->db->prefix('group_permission'));
        if (null !== $criteria && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
