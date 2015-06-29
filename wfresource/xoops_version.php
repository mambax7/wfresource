<?php
/**
 * Name: xoops_version.php
 * Description:
 *
 * @package : Xoosla Modules
 * @Module :
 * @subpackage :
 * @since : v1.0.0
 * @author John Neill <catzwolf@xoosla.com>
 * @copyright : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license : GNU/LGPL, see docs/license.php
 * @version : $Id: xoops_version.php 9326 2012-04-14 21:53:58Z beckmi $
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

$modversion['name'] = _MA_WF_RESOURCE;
$modversion['version'] = 1.05;
$modversion['description'] = _MA_WF_RESOURCE_DSC;
$modversion['author'] = 'Catzwolf';
$modversion['credits'] = '';
$modversion['releasedate'] = 'Friday 14.4.2009';

$modversion['help'] = '';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['dirname'] = 'wfresource';
$modversion['image'] = 'images/wfresource_logo.png';
// Admin things
$modversion['hasAdmin'] = 0;

$modversion['templates'][$i]['file'] = 'wfp_addto.html';
$modversion['templates'][$i]['description'] = 'Displays an AddTo bar';

?>