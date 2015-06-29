<?php
/**
 * Name: class.help.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: class.clean.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * wfp_Clean
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: class.clean.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_Clean {
	var $content = null;
	/**
	 * wfp_Clean::wfp_Clean()
	 *
	 * @param string $aboutTitle
	 */
	function __construct() {
	}

	/**
	 * wfp_Clean::fileExists()
	 *
	 * @param mixed $file
	 * @return
	 */
	function getContentsCallback( $file ) {
		static $content = null;
		if ( $content == null ) {
			if ( file_exists( $file ) && is_readable( $file ) ) {
				$this->content = file_get_contents( $file );
			}
		}
	}

	/**
	 * wfp_Clean::getHtml()
	 *
	 * @param string $file
	 * @param string $cleanlevel
	 * @return
	 */
	function getHtml( $file = '', $content = '', $uploaddir = '' ) {
		if ( $file == 'http://' || !$file ) {
			$this->content = $content;
		} else {
			if ( preg_match( "/^[\.]{1,2}$/", $file ) ) {
				$obj->setVar( 'wfc_file', '' );
			}
			$paths = array( $file, XOOPS_ROOT_PATH . '/' . $uploaddir . '/' . $file );
			array_walk_recursive( $paths, array( 'self', 'getContentsCallback' ) );
		}
		return $this->content;
	}

	/**
	 * wfp_Clean::importHtml()
	 *
	 * @return
	 */
	function importHtml( $file = '', $uploaddir = '' ) {
		if ( preg_match( "/^[\.]{1,2}$/", $file ) ) {
			return '';
		}
		/**
		 * Do array walk to get path and contents
		 */
		$paths = array( $file, XOOPS_ROOT_PATH . '/' . $uploaddir . '/' . $file );
		array_walk_recursive( $paths, array( 'self', 'getContentsCallback' ) );
		/**
		 * Do array walk to get path and contents
		 */
		$matches = array();
		preg_match( '/<title>(.*)<\/title>/', $this->content, $matches );
		$content['content'] = $this->content;
		$content['title'] = ( isset( $matches[1] ) ) ? ( string )$matches[1]: '';
		return $content;
	}

	/**
	 * wfp_Clean::cleanUpHTML()
	 *
	 * @param mixed $text
	 * @param mixed $cleanlevel
	 * @return
	 */
	function &cleanUpHTML( $text, $cleanlevel = 0 ) {
		$text = stripslashes( $text );
		$htmltidy = wfp_getClass( 'htmltidy', _RESOURCE_DIR, _RESOURCE_CLASS );

		$htmltidy->Options['UseTidy'] = false;
		$htmltidy->Options['OutputXHTML'] = false;
		$htmltidy->Options['Optimize'] = true;
		$htmltidy->Options['Compress'] = true;
		switch ( $cleanlevel ) {
			case 1:
				$htmltidy->html = $text;
				$text = &$htmltidy->cleanUp();
				break;
			case 2:
				$text = preg_replace( '/\<style[\w\W]*?\<\/style\>/i', '', $text );
				$htmltidy->Options['IsWord'] = true;
				$htmltidy->html = $text;
				$text = &$htmltidy->cleanUp();
				break;
			case 3:
				$text = preg_replace( '/\<style[\w\W]*?\<\/style\>/i', '', $text );
				$htmltidy->Options['IsWord'] = true;
				$htmltidy->html = $text;
				$text = &$htmltidy->cleanUp();
				$text = strip_tags( $text, '<br /><br /><p>' );
				break;
			default:
		} // switch
		return $text;
	}
}

?>