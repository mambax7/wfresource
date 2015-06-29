<?php
/**
 * Name: formselectcategory.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: formselectcheckgroup.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

if ( !class_exists( 'XoopsFormCheckBox' ) ) {
	include_once XOOPS_ROOT_PATH . '/class/xoopsform/formcheckbox.php';
}

/**
 * A select field with a choice of available users
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class XoopsFormSelectCheckGroup extends XoopsFormCheckBox {
	/**
	 * Constructor
	 *
	 * @param string $caption
	 * @param string $name
	 * @param bool $include_anon Include user "anonymous"?
	 * @param mixed $value Pre-selected value (or array of them).
	 * @param int $size Number or rows. "1" makes a drop-down-list.
	 * @param bool $multiple Allow multiple selections?
	 */
	function XoopsFormSelectCheckGroup( $caption, $name, $value = null, $size = 1, $multiple = false ) {
		$member_handler = &xoops_gethandler( 'member' );
		$this->userGroups = $member_handler->getGroupList();

		$this->XoopsFormCheckBox( $caption, $name, $value, '', true );
		$this->columns = 3;
		foreach ( $this->userGroups as $group_id => $group_name ) {
			//if ( $group_id != XOOPS_GROUP_ADMIN ) {
				$this->addOption( $group_id, $group_name );
			//}
		}
	}
}

?>
