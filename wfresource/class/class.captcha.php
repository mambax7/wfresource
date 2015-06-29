<?php
require( XOOPS_ROOT_PATH . '/modules/wfresource/class/captcha/php-captcha.inc.php' );
/**
 * wfp_captcha
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class.captcha.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_captcha extends PhpCaptcha {
    var $_font;
    var $_errors;

    /**
     * wfp_captcha::wfp_captach()
     *
     * @return
     */
    function wfp_captach() {
        $this->_font = array( XOOPS_ROOT_PATH . '/modules/wfresource/class/media/VeraMoBd.ttf' );
    }

    function loadFont( $value = array() ) {
    }

    function create() {
        $oVisualCaptcha = new PhpCaptcha( $aFonts, 150, 40 );
        $oVisualCaptcha->SetFileType( 'png' );
        $oVisualCaptcha->UseColour( true );
        $oVisualCaptcha->Create( 'visual-captcha.php' );
    }

    function validate() {
    }
}

?>