<?php
/**
 * Name: validate_string.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: validate_int.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * xo_Filters_Validate_Int
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: validate_int.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class xo_Filters_Validate_Int extends wfp_Request {
	/**
	 * xo_Filters_Validate_String::render()
	 *
	 * @param mixed $value
	 * @return
	 */
	function doRender( $int = null ) {
		$valid_int = filter_var( $value, FILTER_VALIDATE_INT );
		if ( $valid_int !== false ) {
			return true;
		}
		return false;
	}
}

?>