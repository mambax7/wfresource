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
 * @version : $Id: validate_ip.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * xo_Filters_Validate_String
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: validate_ip.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class xo_Filters_Validate_Url extends wfp_Request {
	/**
	 * xo_Filters_Validate_Url::doRender()
	 *
	 * @param mixed $ipaddress
	 * @param mixed $flags valid flags are FILTER_FLAG_IPV6, FILTER_FLAG_IPV4 FILTER_FLAG_NO_PRIV_RANGE FILTER_FLAG_NO_RES_RANGE
	 * Flags can be an array
	 * @return
	 */
	function doRender( $ipaddress = null , $flags = null ) {
		if ( is_array( $flags ) ) {
			$flags = explode( '|', $flags );
		}
		$valid_url = filter_var( $url, FILTER_VALIDATE_IP, "{$flags}" );
		if ( $valid_url !== false ) {
			return true;
		}
		return false;
	}
}

?>