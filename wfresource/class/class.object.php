<?php
/**
 * wfp_Object
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class.object.php 10055 2012-08-11 12:46:10Z beckmi $
 * @access public
 */

define('XOBJ_DTYPE_BOOL', 12 );
class wfp_Object extends XoopsObject {
	/**
	 * wfp_Object::wfp_Object()
	 */
	function wfp_Object() {
	}

	/**
	 * wfp_Object::formEdit()
	 *
	 * @param mixed $value
	 * @return
	 */
	function formEdit( $value = null ) {
		require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/xoopsformloader.php';

		$module_prefix = substr( get_class( $this ), 0, 4 );
		if ( $module_prefix == 'wfp_' ) {
			$file = XOOPS_ROOT_PATH . '/modules/wfresource/class/classforms/form_' . strtolower( $value ) . '.php';
		} else {
			$file = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . '/class/classforms/form_' . strtolower( $value ) . '.php';
		}
		if ( file_exists( $file ) ) {
			include $file;
		} else {
			trigger_error( "Error: Form for $value not found" );
			return false;
		}
	}

	/**
	 * wfp_Object::getTimeStamp()
	 *
	 * @return
	 */
	function getTimeStamp( $value, $timestamp = '' ) {
		if ( !$this->getVar( $value ) ) {
			return '';
		}
		return formatTimestamp( $this->getVar( $value ), $timestamp );
	}

	/**
	 * wfp_Object::getUerName()
	 *
	 * @param mixed $value
	 * @param string $timestamp
	 * @return
	 */
	function getUserName( $value, $timestamp = '', $usereal = false, $linked = true ) {
		XoopsLoad::load( 'userutility' );
		return XoopsUserUtility::getUnameFromId( $this->getVar( $value ), $usereal, $linked );
	}

	/**
	 * wfp_Object::getUserID()
	 *
	 * @param mixed $value
	 * @param string $timestamp
	 * @param mixed $usereal
	 * @param mixed $linked
	 * @return
	 */
	function getUserID( $value, $timestamp = '', $usereal = false, $linked = false ) {
		if ( !$this->getVar( $value ) ) {
			return ( is_object( $GLOBALS['xoopsUser'] ) ) ? $GLOBALS['xoopsUser']->getVar( 'uid' ) : 0;
		}
		return $this->getVar( $value );
	}

	/**
	 * wfp_Object::getTextbox()
	 *
	 * @param mixed $id
	 * @param mixed $name
	 * @param integer $size
	 * @param mixed $max
	 * @return
	 */
	function getTextbox( $id = null, $name = null, $size = 25, $max = 255 ) {
		$ret = '<input type="text" name="' . $name . '[' . $this->getVar( $id ) . ']" value="' . $this->getVar( $name ) . '" size="' . $size . '" maxlength="' . $max . '" />';
		return $ret;
	}

	/**
	 * wfc_Page::getYesNobox()
	 *
	 * @param mixed $id
	 * @param mixed $name
	 * @param mixed $value
	 * @return
	 */
	function getYesNobox( $id = null, $name = null, $value = null ) {
		$i = $this->getVar( $id );
		$ret = "<input type='radio' name='" . $name . "[" . $i . "]' value='1'";
		$selected = $this->getVar( $name );
		if ( isset( $selected ) && ( 1 == $selected ) ) {
			$ret .= " checked='checked'";
		}
		$ret .= " />" . _YES . "\n";
		$ret .= "<input type='radio' name='" . $name . "[" . $i . "]' value='0'";
		$selected = $this->getVar( $name );
		if ( isset( $selected ) && ( 0 == $selected ) ) {
			$ret .= " checked='checked'";
		}
		$ret .= " />" . _NO . "\n";
		return $ret;
	}

	/**
	 * XoopsObject::getCheckbox()
	 *
	 * @param mixed $id
	 * @return
	 */
	function getCheckbox( $id = null ) {
		$ret = '<input type="checkbox" value="1" name="checkbox[' . $this->getVar( $id ) . ']" />';
		return $ret;
	}

	/**
	 * Display a human readable date form
	 * parm: intval: 	$time	- unix timestamp
	 */
	function formatTimeStamp( $_time = null, $format = 'D, M-d-Y', $err = '-------------' ) {
		if ( is_string( $_time ) && $_time != 'today' ) {
			$_time = $this->getVar( $_time, 'e' );
		} elseif ( is_numeric( $_time ) ) {
			$_time = $_time;
		} elseif ( $_time == 'today' ) {
			$_time = time();
		}
		$ret = ( $_time ) ? formatTimestamp( $_time, $format ) : $err;
		return $ret;
	}

	/**
	 * wfc_Page::toArray()
	 *
	 * @return
	 */
	function toArray() {
		$ret = array();
		$vars = $this->getVars();
		foreach ( array_keys( $vars ) as $i ) {
			$ret[$i] = $this->getVar( $i );
		}
		return $ret;
		unset( $ret );
	}

	/**
	 * wfp_Object::getImage()
	 *
	 * @param mixed $value
	 * @return
	 */
	function getImage( $value, $imagedir = '' ) {
		if ( empty ( $value ) || empty( $imagedir ) ) {
			// trigger_error( 'Required value missing' );
			return false;
		}

		$cleani = explode( '|', $this->getVar( $value ) );
		$orginalImage = ( is_array( $cleani ) ) ? $cleani[0] : $this->getVar( $value ) ;
		if ( !empty( $orginalImage ) && preg_match( '/^blank\./', $orginalImage ) ) {
			return false;
		}

		$imageArray = explode( '|', $this->getVar( $value ) );
		$image['image'] = ( isset( $imageArray[0] ) ) ? $imageArray[0] : $orginalImage;
		$image['width'] = ( !empty( $imageArray[1] ) ) ? $imageArray[1] : 300;
		$image['height'] = ( !empty( $imageArray[2] ) ) ? $imageArray[2] : 250;
		$image['url'] = XOOPS_URL . '/' . $imagedir . '/' . $image['image'];
		if ( !file_exists( XOOPS_ROOT_PATH . '/' . $imagedir . '/' . $image['image'] ) || is_dir( XOOPS_ROOT_PATH . '/' . $imagedir . '/' . $image['image'] ) ) {
			// trigger_error( 'Image : ' . $image['url'] . ' was not found at the located area of your server.' );
			unset( $image, $orginalImage, $cleani, $imagedir, $value );
			return false;
		}
		return $image;
	}

	/**
	 * wfp_Object::getImageEdit()
	 *
	 * @param mixed $value
	 * @return
	 */
	function getImageEdit( $value ) {
		$cleani = explode( '|', $this->getVar( $value ) );
		$orginalImage = ( is_array( $cleani ) ) ? $cleani[0] : $this->getVar( $value ) ;
		if ( !empty( $orginalImage ) && preg_match( '/^blank\./', $orginalImage ) ) {
			return false;
		}

		$imageArray = explode( '|', $this->getVar( $value ) );
		$image['image'] = ( isset( $imageArray[0] ) ) ? $imageArray[0] : $orginalImage;
		$image['width'] = ( !empty( $imageArray[1] ) ) ? $imageArray[1] : 300;
		$image['height'] = ( !empty( $imageArray[2] ) ) ? $imageArray[2] : 250;
		return $image;
	}
}

?>