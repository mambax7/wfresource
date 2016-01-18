<?php
/**
 * Name: xoops_version.php
 * Description:
 *
 * @package    : Xoosla Modules
 * @Module     :
 * @subpackage :
 * @since      : v1.0.0
 * @author     John Neill <catzwolf@xoosla.com>
 * @copyright  : Copyright (C) 2009 Xoosla. All rights reserved.
 * @license    : GNU/LGPL, see docs/license.php
 * @version    : $Id: xoops_version.php 9326 2012-04-14 21:53:58Z beckmi $
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$modversion['name']        = _MD_WF_RESOURCE;
$modversion['version']     = 1.06;
$modversion['description'] = _MD_WF_RESOURCE_DSC;
$modversion['author']      = 'Catzwolf';
$modversion['credits']     = 'Mark Boyden, Mamba';
$modversion['releasedate'] = 'NOT RELEASED';
$modversion['status']      = '1.06 Beta';

$modversion['help']     = '';
$modversion['license']  = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['dirname']  = 'wfresource';
$modversion['image']    = 'images/module_logo.png';
// Admin things
$modversion['hasAdmin'] = 0;

$modversion['templates'][1]['file']        = 'wfp_addto.tpl';
$modversion['templates'][1]['description'] = 'Displays an AddTo bar';
