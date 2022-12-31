<?php declare(strict_types=1);

namespace XoopsModules\Wfresource\Captcha;

/* PhpCaptcha - A visual and audio CAPTCHA generation library

      Software License Agreement (BSD License)

      Copyright (C) 2005-2006, Edward Eliot.
      All rights reserved.

      Redistribution and use in source and binary forms, with or without
      modification, are permitted provided that the following conditions are met:

         * Redistributions of source code must retain the above copyright
           notice, this list of conditions and the following disclaimer.
         * Redistributions in binary form must reproduce the above copyright
           notice, this list of conditions and the following disclaimer in the
           documentation and/or other materials provided with the distribution.
         * Neither the name of Edward Eliot nor the names of its contributors
           may be used to endorse or promote products derived from this software
           without specific prior written permission of Edward Eliot.

      THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
      EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
      WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
      DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
      DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
      (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
      LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
      ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
      (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
      SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

      Last Updated:  18th April 2006                               */

/**
 * *********************** Documentation ***********************
 */
/*

   Documentation is available at https://www.ejeliot.com/pages/2

   */
/**
 * *********************** Default Options *********************
 */
// start a PHP session - this class uses sessions to store the generated
// code. Comment out if you are calling already from your application
//session_start();
// class defaults - change to effect globally
\define('CAPTCHA_SESSION_ID', 'php_captcha');
\define('CAPTCHA_WIDTH', 200); // max 500
\define('CAPTCHA_HEIGHT', 50); // max 200
\define('CAPTCHA_NUM_CHARS', 5);
\define('CAPTCHA_NUM_LINES', 70);
\define('CAPTCHA_CHAR_SHADOW', false);
\define('CAPTCHA_OWNER_TEXT', '');
\define('CAPTCHA_CHAR_SET', ''); // defaults to A-Z
\define('CAPTCHA_CASE_INSENSITIVE', true);
\define('CAPTCHA_BACKGROUND_IMAGES', '');
\define('CAPTCHA_MIN_FONT_SIZE', 16);
\define('CAPTCHA_MAX_FONT_SIZE', 25);
\define('CAPTCHA_USE_COLOUR', false);
\define('CAPTCHA_FILE_TYPE', 'jpeg');
\define('CAPTCHA_FLITE_PATH', '/usr/bin/flite');
\define('CAPTCHA_AUDIO_PATH', '/tmp/'); // must be writeable by PHP process
/**
 * *********************** End Default Options *********************
 */
// don't edit below this line (unless you want to change the class!)
class PhpCaptcha
{
    public $oImage;
    public $aFonts;
    public $iWidth;
    public $iHeight;
    public $iNumChars;
    public $iNumLines;
    public $iSpacing;
    public $bCharShadow;
    public $sOwnerText;
    public $aCharSet;
    public $bCaseInsensitive;
    public $vBackgroundImages;
    public $iMinFontSize;
    public $iMaxFontSize;
    public $bUseColour;
    public $sFileType;
    public $sCode = '';

    /**
     * @param     $aFonts
     * @param int $iWidth
     * @param int $iHeight
     */
    public function __construct(
        $aFonts, // array of TrueType fonts to use - specify full path
        $iWidth = CAPTCHA_WIDTH, // width of image
        $iHeight = CAPTCHA_HEIGHT // height of image
    )
    {
        // get parameters
        $this->aFonts = $aFonts;
        $this->setNumChars(CAPTCHA_NUM_CHARS);
        $this->setNumLines(CAPTCHA_NUM_LINES);
        $this->displayShadow(CAPTCHA_CHAR_SHADOW);
        $this->setOwnerText(CAPTCHA_OWNER_TEXT);
        $this->setCharSet(CAPTCHA_CHAR_SET);
        $this->caseInsensitive(CAPTCHA_CASE_INSENSITIVE);
        $this->setBackgroundImages(CAPTCHA_BACKGROUND_IMAGES);
        $this->setMinFontSize(CAPTCHA_MIN_FONT_SIZE);
        $this->setMaxFontSize(CAPTCHA_MAX_FONT_SIZE);
        $this->useColour(CAPTCHA_USE_COLOUR);
        $this->setFileType(CAPTCHA_FILE_TYPE);
        $this->setWidth($iWidth);
        $this->setHeight($iHeight);
    }

    public function calculateSpacing(): void
    {
        $this->iSpacing = (int)($this->iWidth / $this->iNumChars);
    }

    /**
     * @param $iWidth
     */
    public function setWidth($iWidth): void
    {
        $this->iWidth = $iWidth;
        if ($this->iWidth > 500) {
            $this->iWidth = 500;
        } // to prevent perfomance impact
        $this->calculateSpacing();
    }

    /**
     * @param $iHeight
     */
    public function setHeight($iHeight): void
    {
        $this->iHeight = $iHeight;
        if ($this->iHeight > 200) {
            $this->iHeight = 200;
        } // to prevent performance impact
    }

    /**
     * @param $iNumChars
     */
    public function setNumChars($iNumChars): void
    {
        $this->iNumChars = $iNumChars;
        $this->calculateSpacing();
    }

    /**
     * @param $iNumLines
     */
    public function setNumLines($iNumLines): void
    {
        $this->iNumLines = $iNumLines;
    }

    /**
     * @param $bCharShadow
     */
    public function displayShadow($bCharShadow): void
    {
        $this->bCharShadow = $bCharShadow;
    }

    /**
     * @param $sOwnerText
     */
    public function setOwnerText($sOwnerText): void
    {
        $this->sOwnerText = $sOwnerText;
    }

    /**
     * @param $vCharSet
     */
    public function setCharSet($vCharSet): void
    {
        // check for input type
        if (\is_array($vCharSet)) {
            $this->aCharSet = $vCharSet;
        } else {
            if ('' !== $vCharSet) {
                // split items on commas
                $aCharSet = \explode(',', $vCharSet);
                // initialise array
                $this->aCharSet = [];
                // loop through items
                foreach ($aCharSet as $sCurrentItem) {
                    // a range should have 3 characters, otherwise is normal character
                    if (3 === mb_strlen($sCurrentItem)) {
                        // split on range character
                        $aRange = \explode('-', $sCurrentItem);
                        // check for valid range
                        if ($aRange[0] < $aRange[1] && 2 === \count($aRange)) {
                            // create array of characters from range
                            $aRange = \range($aRange[0], $aRange[1]);
                            // add to charset array
                            $this->aCharSet = \array_merge($this->aCharSet, $aRange);
                        }
                    } else {
                        $this->aCharSet[] = $sCurrentItem;
                    }
                }
            }
        }
    }

    /**
     * @param $bCaseInsensitive
     */
    public function caseInsensitive($bCaseInsensitive): void
    {
        $this->bCaseInsensitive = $bCaseInsensitive;
    }

    /**
     * @param $vBackgroundImages
     */
    public function setBackgroundImages($vBackgroundImages): void
    {
        $this->vBackgroundImages = $vBackgroundImages;
    }

    /**
     * @param $iMinFontSize
     */
    public function setMinFontSize($iMinFontSize): void
    {
        $this->iMinFontSize = $iMinFontSize;
    }

    /**
     * @param $iMaxFontSize
     */
    public function setMaxFontSize($iMaxFontSize): void
    {
        $this->iMaxFontSize = $iMaxFontSize;
    }

    /**
     * @param $bUseColour
     */
    public function useColour($bUseColour): void
    {
        $this->bUseColour = $bUseColour;
    }

    /**
     * @param $sFileType
     */
    public function setFileType($sFileType): void
    {
        // check for valid file type
        $this->sFileType = 'jpeg';
        if (\in_array($sFileType, ['gif', 'png', 'jpeg'], true)) {
            $this->sFileType = $sFileType;
        }
    }

    public function drawLines(): void
    {
        for ($i = 0; $i < $this->iNumLines; ++$i) {
            // allocate colour
            if ($this->bUseColour) {
                $iLineColour = \imagecolorallocate($this->oImage, \random_int(100, 250), \random_int(100, 250), \random_int(100, 250));
            } else {
                $iRandColour = \random_int(100, 250);
                $iLineColour = \imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
            }
            // draw line
            \imageline($this->oImage, \random_int(0, $this->iWidth), \random_int(0, $this->iHeight), \random_int(0, $this->iWidth), \random_int(0, $this->iHeight), $iLineColour);
        }
    }

    public function drawOwnerText(): void
    {
        // allocate owner text colour
        $iBlack = \imagecolorallocate($this->oImage, 0, 0, 0);
        // get height of selected font
        $iOwnerTextHeight = \imagefontheight(2);
        // calculate overall height
        $iLineHeight = $this->iHeight - $iOwnerTextHeight - 4;
        // draw line above text to separate from CAPTCHA
        \imageline($this->oImage, 0, $iLineHeight, $this->iWidth, $iLineHeight, $iBlack);
        // write owner text
        \imagestring($this->oImage, 2, 3, $this->iHeight - $iOwnerTextHeight - 3, $this->sOwnerText, $iBlack);
        // reduce available height for drawing CAPTCHA
        $this->iHeight = $this->iHeight - $iOwnerTextHeight - 5;
    }

    public function generateCode(): void
    {
        // reset code
        $this->sCode = '';
        // loop through and generate the code letter by letter
        for ($i = 0; $i < $this->iNumChars; ++$i) {
            if (\count($this->aCharSet) > 0) {
                // select random character and add to code string
                $this->sCode .= $this->aCharSet[\array_rand($this->aCharSet)];
            } else {
                // select random character and add to code string
                $this->sCode .= \chr(\random_int(65, 90));
            }
        }
        // save code in session variable
        if ($this->bCaseInsensitive) {
            $_SESSION[CAPTCHA_SESSION_ID] = \mb_strtoupper($this->sCode);
        } else {
            $_SESSION[CAPTCHA_SESSION_ID] = $this->sCode;
        }
    }

    public function drawCharacters(): void
    {
        // loop through and write out selected number of characters
        $arrayCount = mb_strlen($this->sCode);
        for ($i = 0; $i < $arrayCount; ++$i) {
            // select random font
            $sCurrentFont = $this->aFonts[\array_rand($this->aFonts)];
            // select random colour
            if ($this->bUseColour) {
                $iTextColour = \imagecolorallocate($this->oImage, \random_int(0, 100), \random_int(0, 100), \random_int(0, 100));

                if ($this->bCharShadow) {
                    // shadow colour
                    $iShadowColour = \imagecolorallocate($this->oImage, \random_int(0, 100), \random_int(0, 100), \random_int(0, 100));
                }
            } else {
                $iRandColour = \random_int(0, 100);
                $iTextColour = \imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);

                if ($this->bCharShadow) {
                    // shadow colour
                    $iRandColour   = \random_int(0, 100);
                    $iShadowColour = \imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
                }
            }
            // select random font size
            $iFontSize = \random_int($this->iMinFontSize, $this->iMaxFontSize);
            // select random angle
            $iAngle = \random_int(-30, 30);
            // get dimensions of character in selected font and text size
            $aCharDetails = \imageftbbox($iFontSize, $iAngle, $sCurrentFont, $this->sCode[$i], []);
            // calculate character starting coordinates
            $iX          = $this->iSpacing / 4 + $i * $this->iSpacing;
            $iCharHeight = $aCharDetails[2] - $aCharDetails[5];
            $iY          = $this->iHeight / 2 + $iCharHeight / 4;
            // write text to image
            \imagefttext($this->oImage, $iFontSize, $iAngle, $iX, $iY, $iTextColour, $sCurrentFont, $this->sCode[$i], []);

            if ($this->bCharShadow) {
                $iOffsetAngle = \random_int(-30, 30);

                $iRandOffsetX = \random_int(-5, 5);
                $iRandOffsetY = \random_int(-5, 5);

                \imagefttext($this->oImage, $iFontSize, $iOffsetAngle, $iX + $iRandOffsetX, $iY + $iRandOffsetY, $iShadowColour, $sCurrentFont, $this->sCode[$i], []);
            }
        }
    }

    /**
     * @param $sFilename
     */
    public function writeFile($sFilename): void
    {
        if ('' === $sFilename) {
            // tell browser that data is jpeg
            \header("Content-type: image/$this->sFileType");
        }

        switch ($this->sFileType) {
            case 'gif':
                '' !== $sFilename ? \imagegif($this->oImage, $sFilename) : \imagegif($this->oImage);
                break;
            case 'png':
                '' !== $sFilename ? \imagepng($this->oImage, $sFilename) : \imagepng($this->oImage);
                break;
            default:
                '' !== $sFilename ? \imagejpeg($this->oImage, $sFilename) : \imagejpeg($this->oImage);
        }
    }

    /**
     * @param string $sFilename
     * @return bool
     */
    public function create($sFilename = '')
    {
        // check for required gd functions
        if (!\function_exists('imagecreate') || !\function_exists("image$this->sFileType")
            || ('' !== $this->vBackgroundImages && !\function_exists('imagecreatetruecolor'))) {
            return false;
        }
        // get background image if specified and copy to CAPTCHA
        if (\is_array($this->vBackgroundImages) || '' !== $this->vBackgroundImages) {
            // create new image
            $this->oImage = \imagecreatetruecolor($this->iWidth, $this->iHeight);
            // create background image
            if (\is_array($this->vBackgroundImages)) {
                $iRandImage       = \array_rand($this->vBackgroundImages);
                $oBackgroundImage = \imagecreatefromjpeg($this->vBackgroundImages[$iRandImage]);
            } else {
                $oBackgroundImage = \imagecreatefromjpeg($this->vBackgroundImages);
            }
            // copy background image
            \imagecopy($this->oImage, $oBackgroundImage, 0, 0, 0, 0, $this->iWidth, $this->iHeight);
            // free memory used to create background image
            \imagedestroy($oBackgroundImage);
        } else {
            // create new image
            $this->oImage = \imagecreate($this->iWidth, $this->iHeight);
        }
        // allocate white background colour
        \imagecolorallocate($this->oImage, 255, 255, 255);
        // check for owner text
        if ('' !== $this->sOwnerText) {
            $this->drawOwnerText();
        }
        // check for background image before drawing lines
        if (!\is_array($this->vBackgroundImages) && '' === $this->vBackgroundImages) {
            $this->drawLines();
        }

        $this->generateCode();
        $this->drawCharacters();
        // write out image to file or browser
        $this->writeFile($sFilename);
        // free memory used in creating image
        \imagedestroy($this->oImage);

        return true;
    }

    // call this method statically

    /**
     * @param            $sUserCode
     * @param bool|true  $bCaseInsensitive
     * @return bool
     */
    public function validate($sUserCode, $bCaseInsensitive = true)
    {
        if ($bCaseInsensitive) {
            $sUserCode = \mb_strtoupper($sUserCode);
        }

        if (!empty($_SESSION[CAPTCHA_SESSION_ID]) && $sUserCode === $_SESSION[CAPTCHA_SESSION_ID]) {
            // clear to prevent re-use
            unset($_SESSION[CAPTCHA_SESSION_ID]);

            return true;
        }

        return false;
    }
}
