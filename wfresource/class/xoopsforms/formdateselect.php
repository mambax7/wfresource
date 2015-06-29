<?php
// $Id: formdateselect.php 8181 2011-11-07 01:14:53Z beckmi $
defined( 'XOOPS_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 * XoopsFormDateSelect
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: formdateselect.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 **/
class XoopsFormDateSelect extends XoopsFormText {
    /**
     * XoopsFormDateSelect::XoopsFormDateSelect()
     *
     * @param mixed $caption
     * @param mixed $name
     * @param integer $size
     * @param mixed $value
     * @param mixed $dotime
     **/
    function XoopsFormDateSelect( $caption, $name, $size = 20, $value = null, $dotime = true ) {
		if ( is_numeric( $value ) && intval( $value ) > 4000 ) {
            $value = date( "Y-m-d H:i:s", intval( $value ) );
        } else {
            $value = ( $dotime ) ? date( "Y-m-d H:i:s",  time() ) : null;
        }
        $this->XoopsFormText( $caption, $name, $size, 25, $value );
    }

    /**
     * XoopsFormDateSelect::render()
     *
     * @return
     **/
    function render() {
        $jstime = formatTimestamp( $this->getValue(), 'F j Y, H:i:s' );
        include_once XOOPS_ROOT_PATH . '/modules/wfresource/include/calendarjs.php';
        return "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() . " />&nbsp;<input type='reset' value=' ... ' onclick='return showCalendar(\"" . $this->getName() . "\");'>";
    }
}

?>