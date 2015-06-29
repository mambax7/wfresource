<?php
/**
 * Name: form.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: form.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

require_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';

/**
 * wfp_Form
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: form.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_Form extends XoopsForm {
	var $_tabs;

	/**
	 * wfp_Form::doTabs()
	 *
	 * @return
	 */
	function doTabs() {
		require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsforms/formtabs.php';
		$this->_tabs = new XoopsFormTab( false );
	}
}

?>