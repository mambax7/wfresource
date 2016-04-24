<?php
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

/**
 *
 * @copyright 2006
 */
class XoopsFormImagever extends XoopsFormElement
{
    /**
     * initial background image
     *
     * @var string
     * @access private
     */
    public $_background = 0;

    /**
     * initial content
     *
     * @var string
     * @access private
     */
    public $_value;

    /**
     * Constuctor
     *
     * @param string     $caption   caption
     * @param string     $name      name
     * @param            $size
     * @param            $maxlength
     * @param string     $value     initial content
     * @param int|string $bgimage   number of rows
     */
    public function __construct($caption, $name, $size, $maxlength, $value = '', $bgimage = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setValue($value);
        $this->setBackGround($bgimage);
        $this->_size      = (int)$size;
        $this->_maxlength = (int)$maxlength;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial text value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Get initial _background
     *
     * @return string
     */
    public function getBackGround()
    {
        return $this->_background;
    }

    /**
     * Set initial _background
     *
     * @param  $value string
     */
    public function setBackGround($value)
    {
        $this->_background = $value;
    }

    /**
     * @return int|string
     */
    public function getRand()
    {
        $alphanum = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        switch (2) {
            case 2:
                $this->_rand = substr(str_shuffle($alphanum), 0, 5);
                break;
            case 3:
                $this->_rand = substr(str_shuffle($alphanum), 0, 5);
                break;
            case 1:
            default:
                $this->_rand = mt_rand(10000, 99999);
                break;
        } // switch

        return $this->_rand;
    }

    /**
     * @return string
     */
    public function render()
    {
        // return "<div><input type='text' name='" . $this -> getName() . "' id='" . $this -> getName() . "' size='" . $this -> getSize() . "' maxlength='" . $this -> getMaxlength() . "' value='" . $this -> getValue() . "'" . $this -> getExtra() . " />";
        $ret = "<div><input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() . ' />';
        $ret .= '<br />';
        $ret .= "<div style='padding: 8px;'><img src='" . $this->getImage() . ".jpg' /></div>";
        $ret .= "<input type='hidden' name='image_random_value' id='image_random_value' value='" . $this->getRand() . "' /></div>";

        return $ret;
    }

    public function getImage()
    {
        $this->_image = (!$this->getBackGround()) ? imagecreate(60, 30) : imagecreatefromjpeg('images/' . $this->getBackGround());
        $this->_image = imagecreate(60, 30);
        /*
        * use white as the background image
        */
        $bgColor = imagecolorallocate($this->_image, 255, 255, 255);
        /*
        *  the text color is black
        */
        $textColor = imagecolorallocate($this->_image, 0, 0, 0);
        /*
        *  write the random number
        */
        imagestring($this->_image, 5, 5, 8, $this->getRand(), $textColor);
        // $thumb = ImageCreateTrueColor( 60, 30 );
        // imagecopyresized( $thumb, $this -> _image, 0, 0, 0, 0, 60, 30, 60, 30 );
        /*
        *  send several headers to make sure the image is not cached
        *  taken directly from the PHP Manual
        *  Date in the past
        */
        if (!headers_sent()) {
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Content-type: image/jpeg');
        }
        // imagejpeg( $this -> _image );
        // ob_start(); // start a new output buffer
        // imagejpeg( $this -> _image );
        // imagejpeg( $thumb, null, 90 );
        // $ImageData = ob_get_contents();
        // $ImageDataLength = ob_get_length();
        // ob_end_clean(); // stop this output buffer
        imagejpeg($this->_image);
        imagedestroy($this->_image);
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
