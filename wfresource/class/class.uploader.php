<?php
// $Id: class.uploader.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// Xoops - PHP Content Management System                      			//
// Copyright (c) 2007 Xoops                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.xoops.com 												//
// Project: Xoops Project                                               //
// -------------------------------------------------------------------------//
/*!
/**
 * Upload Media files
 *
 * Example of usage:
 * <code>
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new wfp_Uploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
 *      if (!$uploader->upload()) {
 *         echo $uploader->getErrors();
 *      } else {
 *         echo '<h4>File uploaded successfully!</h4>'
 *         echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 *         echo 'Full path: ' . $uploader->getSavedDestination();
 *      }
 * } else {
 *      echo $uploader->getErrors();
 * }
 * </code>
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Xoops Project - http.www.xoops.com
 */
class wfp_Uploader {
    var $mediaName;
    var $mediaType;
    var $mediaSize;
    var $mediaExt;
    var $mediaTmpName;
    var $mediaError;
    var $uploadDir = '';
    var $allowedMimeTypes = array();
    var $maxFileSize = 0;
    var $maxWidth;
    var $maxHeight;
    var $targetFileName;
    var $prefix;
    var $errors = array();
    var $savedDestination;
    var $savedFileName;

    /**
     * Constructor
     *
     * @param string $uploadDir
     * @param array $allowedMimeTypes
     * @param int $maxFileSize
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $cmodvalue
     */
    function wfp_Uploader( $uploadDir, $allowedMimeTypes, $maxFileSize, $maxWidth = null, $maxHeight = null ) {
        if ( is_array( $allowedMimeTypes ) ) {
            $this->allowedMimeTypes = &$allowedMimeTypes;
        }
        $this->uploadDir = $uploadDir;
        $this->maxFileSize = intval( $maxFileSize );
        if ( isset( $maxWidth ) ) {
            $this->maxWidth = intval( $maxWidth );
        }
        if ( isset( $maxHeight ) ) {
            $this->maxHeight = intval( $maxHeight );
        }
    }

    /**
     * Fetch the uploaded file
     *
     * @param string $media_name Name of the file field
     * @param int $index Index of the file (if more than one uploaded under that name)
     * @return bool
     */
    function fetchMedia( &$media_name, $index = null ) {
        if ( is_array( $media_name ) && !empty( $media_name ) ) {
            $this->mediaName = ( get_magic_quotes_gpc() ) ? stripslashes( $media_name['name'] ) : $media_name['name'];
            $this->mediaName = $media_name['name'];
            $this->mediaType = $media_name['type'];
            $this->mediaSize = $media_name['size'];
            $this->mediaExt = ltrim( strrchr( $media_name['name'], '.' ), '.' );
            $this->mediaTmpName = $media_name['tmp_name'];
            $this->mediaError = !empty( $media_name['error'] ) ? $media_name['error'] : 0;
        }
        $this->errors = array();
        if ( intval( $this->mediaSize ) < 0 ) {
            $this->setErrors( 'Invalid File Size' );
            return false;
        }
        if ( $this->mediaName == '' ) {
            $this->setErrors( 'Filename Is Empty' );
            return false;
        }
        if ( preg_match( '/\.(php|cgi|pl|py|asp)$/i', $this->mediaName ) ) {
            $this->setErrors( 'Filename rejected' );
            return false;
        }
        if ( $this->mediaTmpName == 'none' || !is_uploaded_file( $this->mediaTmpName ) ) {
            $this->setErrors( 'No file uploaded' );
            return false;
        }
        if ( $this->mediaError > 0 ) {
            $this->setErrors( 'Error occurred: Error #' . $this->mediaError );
            return false;
        }
        return true;
    }

    /**
     * Set the target filename
     *
     * @param string $value
     */
    function setTargetFileName( $value ) {
        $this->targetFileName = strval( trim( $value ) );
    }

    /**
     * Set the prefix
     *
     * @param string $value
     */
    function setPrefix( $value ) {
        $this->prefix = strval( trim( $value ) );
    }

    /**
     * Get the uploaded filename
     *
     * @return string
     */
    function getMediaName() {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return string
     */
    function getMediaType() {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return int
     */
    function getMediaSize() {
        return $this->mediaSize;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return int
     */
    function getMediaExt() {
        return $this->mediaExt;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return string
     */
    function getMediaTmpName() {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return string
     */
    function getSavedFileName() {
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return string
     */
    function getSavedDestination() {
        return $this->savedDestination;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @return bool
     */
    function upload( $chmod = 0644 ) {
        if ( $this->uploadDir == '' ) {
            $this->setErrors( 'Upload directory not set' );
            return false;
        }
        if ( !is_dir( $this->uploadDir ) ) {
            $this->setErrors( 'Failed opening directory: ' . $this->uploadDir );
        }
        if ( !is_writeable( $this->uploadDir ) ) {
            $this->setErrors( 'Failed opening directory with write permission: ' . $this->uploadDir );
        }
        if ( !$this->checkMaxFileSize() ) {
            $this->setErrors( 'File size too large: ' . $this->mediaSize );
        }
        if ( !$this->checkMaxWidth() ) {
            $this->setErrors( sprintf( 'File width must be smaller than %u', $this->maxWidth ) );
        }
        if ( !$this->checkMaxHeight() ) {
            $this->setErrors( sprintf( 'File height must be smaller than %u', $this->maxHeight ) );
        }
        if ( !$this->checkMimeType() ) {
            $this->setErrors( 'MIME type not allowed: ' . $this->mediaType );
        }
        if ( count( $this->errors ) > 0 ) {
            return false;
        }
        if ( !$this->_copyFile( $chmod ) ) {
            $this->setErrors( 'Failed uploading file: ' . $this->mediaName );
            return false;
        }
        return true;
    }

    /**
     * Copy the file to its destination
     *
     * @return bool
     */
    function _copyFile( $chmod ) {
        $matched = array();
        if ( !preg_match( "/\.([a-zA-Z0-9]+)$/", $this->mediaName, $matched ) ) {
            return false;
        }
        if ( isset( $this->targetFileName ) ) {
            $this->savedFileName = $this->targetFileName;
        } elseif ( isset( $this->prefix ) ) {
            $this->savedFileName = uniqid( $this->prefix ) . '.' . strtolower( $matched[1] );
        } else {
            $this->savedFileName = strtolower( $this->mediaName );
        }

        $this->savedDestination = $this->uploadDir . '/' . $this->savedFileName;
        if ( !move_uploaded_file( $this->mediaTmpName, $this->savedDestination ) ) {
            return false;
        }
        @chmod( $this->savedDestination, $chmod );
        return true;
    }

    /**
     * Is the file the right size?
     *
     * @return bool
     */
    function checkMaxFileSize() {
        if ( $this->mediaSize > $this->maxFileSize ) {
            return false;
        }
        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return bool
     */
    function checkMaxWidth() {
        if ( !isset( $this->maxWidth ) ) {
            return true;
        }
        if ( false !== $dimension = getimagesize( $this->mediaTmpName ) ) {
            if ( $dimension[0] > $this->maxWidth ) {
                return false;
            }
        } else {
            trigger_error( sprintf( 'Failed fetching image size of %s, skipping max width check..', $this->mediaTmpName ), E_USER_WARNING );
        }
        return true;
    }

    /**
     * Is the picture the right height?
     *
     * @return bool
     */
    function checkMaxHeight() {
        if ( !isset( $this->maxHeight ) ) {
            return true;
        }
        if ( false !== $dimension = getimagesize( $this->mediaTmpName ) ) {
            if ( $dimension[1] > $this->maxHeight ) {
                return false;
            }
        } else {
            trigger_error( sprintf( 'Failed fetching image size of %s, skipping max height check..', $this->mediaTmpName ), E_USER_WARNING );
        }
        return true;
    }

    /**
     * Is the file the right Mime type
     *
     * (is there a right type of mime? ;-)
     *
     * @return bool
     */
    function checkMimeType() {
        if ( count( $this->allowedMimeTypes ) > 0 && !in_array( $this->mediaType, $this->allowedMimeTypes ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    function setErrors( $error ) {
        $this->errors[] = trim( $error );
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    function getErrors() {
        return $this->errors;
    }

    /**
     * Get generated errors
     *
     * @param bool $ashtml Format using HTML?
     * @return array |string    Array of array messages OR HTML string
     */
    function &getHtmlErrors( $ashtml = false ) {
        if ( !$ashtml ) {
            return $this->errors;
        } else {
            $ret = '';
            if ( count( $this->errors ) > 0 ) {
                $ret = '<h4>Errors Returned While Uploading</h4>';
                foreach ( $this->errors as $error ) {
                    $ret .= $error . '<br />';
                }
            }
            echo $ret;
        }
    }
}

?>