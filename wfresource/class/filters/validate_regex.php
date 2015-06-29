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
 * @version : $Id: validate_regex.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * xo_Filters_Validate_String
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: validate_regex.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class xo_Filters_Validate_Regex extends wfp_Request {
	/**
	 * xo_Filters_Validate_String::doRender()
	 *
	 * @param mixed $method
	 * @param mixed $key
	 * @param array $options
	 * @return
	 */
	function doRender( $reg = null ) {
		$valid_reg = @filter_var( $reg, FILTER_VALIDATE_REGEXP );
		if ( $valid_reg !== false ) {
			return true;
		}
		return false;
	}
}

?>