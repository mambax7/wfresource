<?php
// $Id: class.pdf.php 8181 2011-11-07 01:14:53Z beckmi $
// ------------------------------------------------------------------------ //
// WF-Channel - WF-Projects													//
// Copyright (c) 2007 WF-Channel											//
// //
// Authors:																	//
// John Neill ( AKA Catzwolf )												//
// //
// URL: http://catzwolf.x10hosting.com/										//
// Project: WF-Projects														//
// -------------------------------------------------------------------------//
defined( 'XOOPS_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * wfp_dopdf
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: class.pdf.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wfp_dopdf {
	var $options = array();
	var $compression = false;
	var $font = 'Helvetica.afm';

	/**
	 * wfp_dopdf::wfp_dopdf()
	 *
	 * @param array $opt
	 */
	function wfp_dopdf( $opt = array() ) {
		if ( !is_array( $opt ) || empty( $opt ) ) {
			return false;
		}
		$this->options = $opt;
	}

	/**
	 * wfp_dopdf::renderpdf()
	 *
	 * @return
	 */
	function renderpdf() {
		if ( isset( $this->options['path'] ) && isset( $this->options['filename'] ) ) {
			$this->destination_file = $this->options['path'] . $this->options['filename'] . '.pdf';
			$this->stdoutput = file_get_contents( $this->destination_file );
			if ( $this->stdoutput ) {
				self::xo_Display();
				exit();
			}
		}

		require_once XOOPS_ROOT_PATH . '/modules/wfresource/class/pdf/class.ezpdf.php';
		$pdf = new Cezpdf( 'a4', 'P' ); //A4 Portrait
		$pdf->options['compression'] = $this->compression;
		$pdf->ezSetCmMargins( 2, 1.5, 1, 1 );
		// select font
		$pdf->selectFont( XOOPS_ROOT_PATH . '/modules/wfresource/class/pdf/fonts/' . $this->font, _CHARSET ); //choose font
		$all = $pdf->openObject();
		$pdf->saveState();
		$pdf->setStrokeColor( 0, 0, 0, 1 );
		// footer
		$pdf->addText( 30, 822, 6, $this->options['slogan'] );
		$pdf->line( 10, 40, 578, 40 );
		$pdf->line( 10, 818, 578, 818 );
		// add url to footer
		$pdf->addText( 30, 34, 6, XOOPS_URL );
		// add pdf creater
		$pdf->addText( 250, 34, 6, $this->options['creator'] );
		// add render date to footer
		$pdf->addText( 450, 34, 6, _CONTENT_RENDERED . ' ' . $this->options['renderdate'] );
		$pdf->restoreState();
		$pdf->closeObject();
		$pdf->addObject( $all, 'all' );
		$pdf->ezSetDy( 30 );
		// title
		$pdf->ezText( $this->options['title'], 16 );
		$pdf->ezText( "\n", 6 );
		if ( $this->options['author'] ) {
			$pdf->ezText( _CONTENT_AUTHOR . $this->options['author'], 8 );
		}
		if ( $this->options['pdate'] ) {
			$pdf->ezText( _CONTENT_PUBLISHED . $this->options['pdate'], 8 );
		}
		if ( $this->options['udate'] ) {
			$pdf->ezText( _CONTENT_UPDATED . $this->options['udate'], 8 );
		}
		$pdf->ezText( "\n", 6 );
		if ( $this->options['itemurl'] ) {
			$pdf->ezText( _CONTENT_URL_TOITEM . $this->options['itemurl'], 8 );
			$pdf->ezText( "\n", 6 );
		}

		if ( $this->options['subtitle'] ) {
			$pdf->ezText( $this->options['subtitle'], 14 );
			$pdf->ezText( "\n", 6 );
		}
		$pdf->ezText( $this->options['content'], 10 );
		if ( $this->options['stdoutput'] == 'file' ) {
			$this->stdoutput = $pdf->ezOutput( 0 );
			file_put_contents( $this->destination_file, $this->stdoutput );
			self::xo_Display();
		} else {
			$pdf->ezStream( 1 );
		}
	}

	function xo_Display() {
		header( "Content-type: application/pdf" );
		header( "Content-Length: " . strlen( ltrim( $tmp ) ) );
		$fileName = ( isset( $options['Content-Disposition'] ) ? $options['Content-Disposition'] : 'file.pdf' );
		header( "Content-Disposition: inline; filename=" . $fileName );
		if ( isset( $options['Accept-Ranges'] ) && $options['Accept-Ranges'] == 1 ) {
			header( "Accept-Ranges: " . strlen( ltrim( $tmp ) ) );
		}
		echo $this->stdoutput;
		exit();
	}

	function setTitle( $value = '' ) {
		$this->options['title'] = $value;
	}

	function setSubTitle( $value = '' ) {
		$this->options['subtitle'] = $value;
	}

	function setCreater( $value = '' ) {
		$this->options['creator'] = $value;
	}

	function setSlogan( $value = '' ) {
		$this->options['slogan'] = $value;
	}

	function setAuthor( $value = '' ) {
		$this->options['author'] = $value;
	}

	function setContent( $value = '' ) {
		$this->options['content'] = $value;
	}

	function setPDate( $value = '' ) {
		$this->options['pdate'] = $value;
	}

	function setUDate( $value = '' ) {
		$this->options['udate'] = $value;
	}

	function setFont( $value = '' ) {
		$this->font = strval( trim( $value ) );
	}

	function useCompression( $value = false ) {
		$this->compression = ( $value == true ) ? true : false;
	}
}

?>