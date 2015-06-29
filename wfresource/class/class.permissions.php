<?php
// $Id: class.permissions.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects													//
// Copyright (c) 2007 WF-Channel											//
// //
// Authors:																	//
// John Neill ( AKA Catzwolf )												//
// //
// URL: http://catzwolf.x10hosting.com/										//
// Project: WF-Projects														//
// -------------------------------------------------------------------------//
/**
 * This class is copyright Xoops.com and must remain so.
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * wfp_PermissionsHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: class.permissions.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/grouppermform.php';
class wfp_Permissions extends XoopsGroupPermForm {
	var $db;
	var $db_table;
	var $_mod_id = 0;
	var $_perm_name;
	var $_perm_descript;

	/**
	 * wfp_Permissions::wfp_Permissions()
	 *
	 * @param string $_table
	 * @param string $__perm_name
	 * @param string $__perm_descript
	 * @return
	 */
	function wfp_Permissions() {
		// null value
	}

	/**
	 * wfp_Permissions::setPermissions()
	 *
	 * @param string $_table
	 * @param string $_perm_name
	 * @param string $_perm_descript
	 * @param mixed $_mod_id
	 * @return
	 */
	function setPermissions( $_table = '', $_perm_name = '', $_perm_descript = '', $_mod_id ) {
		if ( !empty( $_table ) ) {
			$this->db = &XoopsDatabaseFactory::getDatabaseConnection();
			$this->db_table = $this->db->prefix( $_table );
		}
		$this->_mod_id = intval( $_mod_id );
		$this->_perm_name = strval( $_perm_name );
		$this->_perm_descript = strval( $_perm_descript );
	}

	/**
	 * wfp_Permissions::wfp_Permissions_render()
	 *
	 * @param array $_arr
	 * @return
	 */
	function render( $_arr = array() ) {
		$ret = '';
		if ( $this->_perm_descript ) {
			$_perm_descript = $this->_perm_descript;
		} else {
			$_perm_descript = null;
		}
		$sql = "SELECT {$_arr['cid']}";
		if ( !empty( $_arr['pid'] ) ) {
			$sql = ", {$_arr['pid']}";
		}
		$sql .= ", {$_arr['title']} FROM " . $this->db_table;

		if ( !empty( $_arr['where'] ) ) {
			$sql .= " WHERE {$_arr['where']}=" . $this->_mod_id;
		}

		if ( !empty( $_arr['order'] ) ) {
			$sql .= " ORDER BY {$_arr['order']}";
		}

		if ( !$result = $this->db->query( $sql ) ) {
			$_error = $this->db->error() . " : " . $this->db->errno();
			trigger_error( $_error );
		}

		$_form_info = new XoopsGroupPermForm( '', $this->_mod_id, $this->_perm_name, $this->_perm_descript );
		if ( $this->db->getRowsNum( $result ) ) {
			while ( $_row_arr = $this->db->fetcharray( $result ) ) {
				if ( !empty( $_arr['pid'] ) ) {
					$_form_info->addItem( $_row_arr[$_arr['cid']], $_row_arr[$_arr['title']], $_row_arr[$_arr['pid']] );
				} else {
					$_form_info->addItem( $_row_arr[$_arr['cid']], $_row_arr[$_arr['title']], 0 );
				}
			}
			$ret = $_form_info->render();
		}
		unset( $_form_info );
		echo $ret;
	}

	/**
	 * wfp_Permissions::save()
	 *
	 * @param array $_groups
	 * @param mixed $_item_id
	 * @return
	 */
	function save( $_groups = array(), $_item_id = 0 ) {
		$_item_id = strval( intval( $_item_id ) );
		if ( !is_array( $_groups ) || !count( $_groups ) || $_item_id == 0 ) {
			return false;
		}

		/**
		 * Save the new permissions
		 */
		$gperm_handler = &wfp_gethandler( 'groupperm' );
		if ( is_object( $gperm_handler ) && !empty( $gperm_handler ) ) {
			/**
			 * First, if the permissions are already there, delete them
			 */
			$gperm_handler->deleteByModule( $this->_mod_id, $this->_perm_name, $_item_id );
			foreach ( $_groups as $_group_id ) {
				if ( !$gperm_handler->addRight( $this->_perm_name, $_item_id, $_group_id, $this->_mod_id ) ) {
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
	 * @param mixed $_item_id
	 * @return
	 */
	function get( $_item_id ) {
		global $xoopsUser;
		$_item_id = strval( intval( $_item_id ) );
		$_groups = ( is_object( $xoopsUser ) ) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
		$gperm_handler = &wfp_gethandler( 'groupperm' );
		if ( $_groups && is_object( $gperm_handler ) ) {
			$ret = $gperm_handler->checkRight( $this->_perm_name, $_item_id , $_groups, $this->_mod_id );
			return $ret;
		}
		return false;
	}

	/**
	 * wfp_Permissions::getAdmin()
	 *
	 * @param mixed $_item_id
	 * @param mixed $isNew
	 * @return
	 */
	function getAdmin( $_item_id, $isNew = null ) {
		$_item_id = intval( $_item_id );
		$gperm_handler = &wfp_gethandler( 'groupperm' );
		$groups = $gperm_handler->getGroupIds( $this->_perm_name, $_item_id, $this->_mod_id );
		if ( !count( $groups ) && $isNew == true ) {
			$groups = array( 0 => 1, 1 => 2 );
		}
		return $groups;
	}

	/**
	 * wfp_Permissions::doDelete()
	 *
	 * @param mixed $_item_id
	 * @return
	 */
	function doDelete( $_item_id ) {
		global $xoopsUser;

		$_item_id = strval( intval( $_item_id ) );
		$_groups = ( is_object( $xoopsUser ) ) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
		$gperm_handler = &wfp_gethandler( 'groupperm' );
		if ( $_groups && is_object( $gperm_handler ) ) {
			$gperm_handler->deleteByModule( $this->_mod_id, $this->_perm_name, $_item_id );
		}
		return false;
	}
}

?>