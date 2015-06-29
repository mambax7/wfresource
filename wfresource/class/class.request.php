<?php
/**
 * Name: class.filter.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: class.request.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * wfp_Filter
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: class.request.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_Filter {
	protected static $instance;
	protected static $handlers;
	private static $name;
	/**
	 * xo_Xoosla::getIntance()
	 *
	 * @return
	 */
	public static function &getInstance() {
		if ( self::$instance == null ) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * wfp_Filter::getFilter()
	 *
	 * @param mixed $name
	 * @return
	 */
	public static function getFilter( $name, $module = null ) {
		static $handlers;
		self::$name = $name;
		/**
		 */
		if ( !isset( $handlers[self::$name] ) ) {
			// $ret = self::getModule( $module );
			// if ( $ret !== false ) {
			$ret = self::getCore();
			// }
			if ( $ret !== true ) {
				$className = 'xo_Filters_' . self::$name;
				if ( class_exists( $className ) && is_callable( __CLASS__, $className ) ) {
					$handler = new $className( __CLASS__ );
					if ( !is_object( $handler ) ) {
						trigger_error( 'value is null, sort it or else sucker' );
						return null;
					}
					$handlers[self::$name] = $handler;
				}
			} else {
				trigger_error( 'Error: Filter <b>' . $name . '</b> could not be load due to an error. Please check the filter name and try again.<br />File: ' . __FILE__ . ' line: ' . __LINE__ );
			}
		}
		unset( $name );
		return $handlers[self::$name];
	}

	/**
	 * wfp_Filter::getCore()
	 *
	 * @return
	 */
	function getCore() {
		if ( file_exists( $file = dirname( __FILE__ ) . DS . 'filters' . DS . strtolower( self::$name ) . '.php' ) ) {
			include_once $file;
		}
		unset( $file );
		return false;
	}

	/**
	 * wfp_Filter::getModule()
	 *
	 * @return
	 */
	function getModule( $module = null ) {
		$module = ( !is_null( $module ) ) ? $module : $GLOBALS['xoopsModule'];
		if ( file_exists( $file = XOOPS_ROOT_PATH . '/modules' . $module . '/filters/' . strtolower( self::$name ) . '.php' ) ) {
			include_once $file;
		} else {
			trigger_error( $file );
		}
		unset( $file );
		return false;
	}

	/**
	 * wfp_Filter::getUser()
	 *
	 * @return
	 */
	function getUser() {
	}

	/**
	 * wfp_Filter::filterValidate()
	 *
	 * @return
	 */
	function filterValidate( $value , $filterid = 0 ) {
		return ( filter_var( $value, ( int )$filterid ) ) ? true : false;
	}
}

/**
 * wfp_Request
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: class.request.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_Request {
	static $method;

	/**
	 * Constructor
	 *
	 * @access protected
	 */
	public function __construct() {
	}

	/**
	 * wfp_Filter::doRequest()
	 *
	 * @param mixed $type
	 * @param mixed $key
	 * @param mixed $default
	 * @param array $filters
	 * @return
	 */
	public static function doRequest( $method, $key, $default = null, $type = null, $options = array(), $module = '' ) {
		if ( ctype_alpha( $type ) ) {
			$filter = wfp_Filter::getFilter( 'Sanitize_' . ucfirst( $type ), $module );
			if ( !empty( $filter ) && is_object( $filter ) ) {
				$ret = $filter->doRender( $method, $key, $options );
				return ( $ret === false ) ? $default : $ret;
			}
		}
		unset( $filter );
		return false;
	}

	/**
	 * wfp_Request::doValidate()
	 *
	 * @return
	 */
	public static function doValidate( $value, $type, $module = '', $flags = null ) {
		if ( ctype_alpha( $type ) ) {
			$filter = wfp_Filter::getFilter( 'Validate_' . ucfirst( $type ), $module );
			if ( !empty( $filter ) && is_object( $filter ) ) {
				if ( $ret = $filter->doRender( $value, $flags ) ) {
					return ( $ret === false ) ? $default : $ret;
				}
			}
		}
		unset( $filter );
		return false;
	}

	/**
	 * wfp_Request::inArray()
	 *
	 * @return
	 */
	public static function inArray( $method, $key ) {
		if ( empty( $method ) || empty( $key ) ) {
			return filter_has_var( $method, $key );
		}
	}
}

?>