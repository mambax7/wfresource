<?php
defined( 'XOOPS_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 *
 * @version $Id: formimagever.php 8181 2011-11-07 01:14:53Z beckmi $
 * @copyright 2006
 */
class XoopsFormImagever extends XoopsFormElement {
    /**
     * initial background image
     *
     * @var string
     * @access private
     */
    var $_background = 0;

    /**
     * initial content
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Constuctor
     *
     * @param string $caption caption
     * @param string $name name
     * @param string $value initial content
     * @param int $bgimage number of rows
     */
    function XoopsFormImagever( $caption, $name, $size, $maxlength, $value = "", $bgimage = "" )
    {
        $this -> setCaption( $caption );
        $this -> setName( $name );
        $this -> setValue( $value );
        $this -> setBackGround( $bgimage );
        $this -> _size = intval( $size );
        $this -> _maxlength = intval( $maxlength );
    }

    /**
     * Get size
     *
     * @return int
     */
    function getSize()
    {
        return $this -> _size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    function getMaxlength()
    {
        return $this -> _maxlength;
    }

    /**
     * Get initial text value
     *
     * @return string
     */
    function getValue()
    {
        return $this -> _value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    function setValue( $value )
    {
        $this -> _value = $value;
    }

    /**
     * Get initial _background
     *
     * @return string
     */
    function getBackGround()
    {
        return $this -> _background;
    }

    /**
     * Set initial _background
     *
     * @param  $value string
     */
    function setBackGround( $value )
    {
        $this -> _background = $value;
    }

    function getRand()
    {
        $alphanum = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        switch ( 2 ) {
            case 2:
                $this -> _rand = substr( str_shuffle( $alphanum ), 0, 5 );
                break;
            case 3:
                $this -> _rand = substr( str_shuffle( $alphanum ), 0, 5 );
                break;
            case 1:
            default:
                $this -> _rand = rand( 10000, 99999 );
                break;
        } // switch
        return $this -> _rand;
    }

    function render()
    {
        // return "<div><input type='text' name='" . $this -> getName() . "' id='" . $this -> getName() . "' size='" . $this -> getSize() . "' maxlength='" . $this -> getMaxlength() . "' value='" . $this -> getValue() . "'" . $this -> getExtra() . " />";
        $ret = "<div><input type='text' name='" . $this -> getName() . "' id='" . $this -> getName() . "' size='" . $this -> getSize() . "' maxlength='" . $this -> getMaxlength() . "' value='" . $this -> getValue() . "'" . $this -> getExtra() . " />";
        $ret .= "<br />";
        $ret .= "<div style='padding: 8px;'><img src='" . $this -> getImage() . ".jpg' /></div>";
        $ret .= "<input type='hidden' name='image_random_value' id='image_random_value' value='" . $this -> getRand() . "' /></div>";
        return $ret;
    }

    function getImage()
    {
        $this -> _image = ( !$this -> getBackGround() ) ? imagecreate( 60, 30 ) : imagecreatefromjpeg( "images/" . $this -> getBackGround() );
        $this -> _image = imagecreate( 60, 30 );
        /*
		* use white as the background image
		*/
        $bgColor = imagecolorallocate ( $this -> _image, 255, 255, 255 );
        /*
		*  the text color is black
		*/
        $textColor = imagecolorallocate ( $this -> _image, 0, 0, 0 );
        /*
		*  write the random number
		*/
        imagestring ( $this -> _image, 5, 5, 8, $this -> getRand(), $textColor );
        // $thumb = ImageCreateTrueColor( 60, 30 );
        // imagecopyresized( $thumb, $this -> _image, 0, 0, 0, 0, 60, 30, 60, 30 );
        /*
		*  send several headers to make sure the image is not cached
		*  taken directly from the PHP Manual
		*  Date in the past
		*/
        if ( !headers_sent() ) {
            header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
            header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
            header( "Cache-Control: no-store, no-cache, must-revalidate" );
            header( "Cache-Control: post-check=0, pre-check=0", false );
            header( "Pragma: no-cache" );
            header( 'Content-type: image/jpeg' );
        }
        // imagejpeg( $this -> _image );
        // ob_start(); // start a new output buffer
        // imagejpeg( $this -> _image );
        // imagejpeg( $thumb, null, 90 );
        // $ImageData = ob_get_contents();
        // $ImageDataLength = ob_get_length();
        // ob_end_clean(); // stop this output buffer
        imagejpeg( $this -> _image );
        imagedestroy( $this -> _image );
        //unset( $rand );

        //$tmpfname = tempnam ( "/tmp", "FOO" );
        //Imagejpeg( $this -> _image, $tmpfname );
        // $temp = fopen($tmpfname,"rb");
        //imagedestroy( $this -> _image );
        // unlink($tmpfname);
        //echo $tmpfname;
        //return $tmpfname;
    }
}

?>