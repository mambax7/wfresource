<?php
/**
 * Name: class.about.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: class.about.php 8181 2011-11-07 01:14:53Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

wfp_loadLangauge( 'about', 'wfresource' );

/**
 * wpf_About
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2009
 * @version $Id: class.about.php 8181 2011-11-07 01:14:53Z beckmi $
 * @access public
 */
class wpf_About {
	/**
	 * wpf_About::wpf_About()
	 */
	function wpf_About() {
	}

	/**
	 * wpf_About::display()
	 *
	 * @return
	 */
	function display() {
		$ret = '';
		$author_name = $GLOBALS['xoopsModule']->getInfo( 'author' ) ? $GLOBALS['xoopsModule']->getInfo( 'author' ) : '';

		$ret .= '<p><img src="' . XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . '/' . $GLOBALS['xoopsModule']->getInfo( 'image' ) . '" align="left" title="' . $GLOBALS['xoopsModule']->getInfo( 'name' ) . '" alt="' . $GLOBALS['xoopsModule']->getInfo( 'name' ) . '" hspace="5" vspace="0" /></a>
				<div style="margin-top: 10px; color: #33538e; margin-bottom: 4px; font-size: 16px; line-height: 16px; font-weight: bold; display: block;">' . $GLOBALS['xoopsModule']->getInfo( 'name' ) . ' version ' . $GLOBALS['xoopsModule']->getInfo( 'version' ) . '</div>
				<div><strong>' . _CP_AM_AB_RELEASEDATE . '</strong> ' . $GLOBALS['xoopsModule']->getInfo( 'releasedate' ) . '</div>
				<div><strong>' . _CP_AM_AB_AUTHOR . '</strong> ' . $author_name . '</div>
				<div>' . $GLOBALS['xoopsModule']->getInfo( 'license' ) . '</div><br />
			</p>';

		$ret .= '<form action="index2.php" method="post" name="adminForm"><table width="100%" cellpadding="2" cellspacing="1">';
		$ret .= $this->about_header( _CP_AM_AB_MAIN_INFO );
		$ret .= $this->about_content( _CP_AM_AB_MODULE , 'name' );
		$ret .= $this->about_content( _CP_AM_AB_DESCRIPTION , 'description' );
		$ret .= $this->about_content( _CP_AM_AB_AUTHOR , 'author' );
		$ret .= $this->about_content( _CP_AM_AB_VERSION , 'version' );
		$ret .= $this->about_content( _CP_AM_AB_STATUS , 'status' );
		$ret .= $this->about_footer();
		/**
		 */
		$ret .= $this->about_header( _CP_AM_AB_DEV_INFO );
		$ret .= $this->about_content( _CP_AM_AB_LEAD , 'lead' );
		$ret .= $this->about_content( _CP_AM_AB_CONTRIBUTORS , 'contributors' );
		$ret .= $this->about_content( _CP_AM_AB_WEBSITE_URL , 'website_url', 'website_name', 'url' );
		$ret .= $this->about_content( _CP_AM_AB_EMAIL , 'email', '', 'email' );
		$ret .= $this->about_content( _CP_AM_AB_CREDITS , 'credits' );
		$ret .= $this->about_content( _CP_AM_AB_LICENSE , 'license' );
		$ret .= $this->about_footer();
		/**
		 */
		$ret .= $this->about_header( _CP_AM_AB_SUPPORT_INFO );
		$ret .= $this->about_content( _CP_AM_AB_DEMO_SITE_URL , 'demo_site_url', 'demo_site_name', 'url' );
		$ret .= $this->about_content( _CP_AM_AB_SUPPORT_SITE_URL , 'support_site_url', 'support_site_name', 'url' );
		$ret .= $this->about_content( _CP_AM_AB_SUBMIT_BUG , 'submit_bug_url', 'submit_bug_name', 'url' );
		$ret .= $this->about_content( _CP_AM_AB_SUBMIT_FEATURE , 'submit_feature_url', 'submit_feature_name', 'url' );
		$ret .= $this->about_footer();
		/**
		 */
		$ret .= $this->about_content( _CP_AM_AB_DISCLAIMER , 'disclaimer', null, null, 1 );
		$ret .= $this->about_footer();
		/**
		 */
		$ret .= $this->about_content( _CP_AM_AB_CHANGELOG , 'changelog', null, null, 1 );
		$ret .= $this->about_footer();
		$ret .= '</table></form>';
		echo $ret;
	}

	/**
	 * wpf_About::about_header()
	 *
	 * @param mixed $heading
	 * @return
	 */
	function about_header( $heading = null ) {
		return '<tr><th colspan="2">' . $heading . '</th></tr>';
	}

	/**
	 * wpf_About::about_content()
	 *
	 * @param string $heading
	 * @param string $value
	 * @param string $value2
	 * @param string $type
	 * @param mixed $colspan
	 * @return
	 */
	function about_content( $heading = '', $value = '', $value2 = '', $type = 'normal', $colspan = null ) {
		$myts = &MyTextSanitizer::getInstance();
		$heading = ( $heading ) ? $heading : '';
		switch ( $type ) {
			case 'normal':
			default:
				$value = ( empty( $value ) ) ? '' : ( $value == 'changelog' ) ? $this->changelog() : $GLOBALS['xoopsModule']->getInfo( $value );
				switch ( $colspan ) {
					case 0:
						return '<tr><td class="head">' . $heading . '</td><td class="even">' . $value . '</td></tr>';
						break;
					case 1:
						return '<tr><th colspan="2">' . $heading . '</th></tr><tr><td colspan="2" class="even">' . $myts->displayTarea( $value ) . '</td></tr>';
						break;
				} // switch
				break;
			case 'url':
				$value = ( $value ) ? $GLOBALS['xoopsModule']->getInfo( $value ) : '';
				$value2 = ( $value2 ) ? $GLOBALS['xoopsModule']->getInfo( $value2 ) : '';
				return '<tr><td class="head">' . $heading . '</td><td class="even"><a href="' . $value . '" target="_blank">' . $value2 . '</a></td></tr>';
				break;
			case 'email':
				$value = ( $value ) ? $GLOBALS['xoopsModule']->getInfo( $value ) : '';
				return '<tr><td class="head">' . $heading . '</td><td class="even"><a href="mailto:' . $value . '">' . $value . '</a></td></tr>';
				break;
		} // switch
	}
	/**
	 * wpf_About::about_footer()
	 *
	 * @return
	 */
	function about_footer() {
		return '<tr><td colspan="2" class="footer">&nbsp;</td></tr>';
	}

	/**
	 * wpf_About::changelog()
	 *
	 * @return
	 */
	function changelog() {
		$file_name = XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar( 'dirname' ) . '/docs/changelog.txt';
		if ( file_exists( $file_name ) && !is_dir( $file_name ) ) {
			// $myts = &MyTextSanitizer::getInstance();
			$file_text = file_get_contents( $file_name );
			// $changelog = $myts->displayTarea( $file_text, $html = 1, $smiley = 0, $xcode = 0, $image = 0, $br = 1 );
			unset( $myts );
		} else {
			$changelog = _CP_AM_AB_NOLOG;
		}
		return $file_text;
	}
}

?>