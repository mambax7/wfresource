<?php declare(strict_types=1);

/**
 * @Xoops     -    PHP Content Management System
 * @copyright 2007 Xoops
 * @Author    :    John (AKA Catzwolf)
 * @URL       :        https://Xoops.com
 * @Project   :    Xoops CMS
 */
defined('XOOPS_ROOT_PATH') || exit('You do not have permission to access this file!');

/**
 * errors
 */
define('_MD_WFP_ERRORS', 'Errors Reported');
define('_MD_WFP_ERROR_CREATE_NEW_OBJECT', 'Could not create new object');
define('_MD_WFP_ERROR_GET_ITEM', 'Error: Requested item could not be found in the database. It does not exist');
define('_MD_WFP_ERROR_DELETE', 'Error: The requested item could not be deleted from the database in file: %s  Line: %s');
define('_MD_WFP_ERROR', 'Error: Could not complete the requested task. <br> ErrorNo: %s  Error: %s<br> In File %s at line %s');
define('_MD_WFP_ISNOTANOBEJECT', 'Error:  Could not create a new item. Could not create new class %s');
define('_MD_WFP_ISNOTOBEJECT', 'Save Failed: Could not save requested item as it is of the wrong type.');
define('_MD_WFP_ISNOTOBEJECTDIRTY', 'Save Failed: Error No: 1000');
define('_MD_WFP_ISNOTOBEJECTCLEAN', 'Save Failed: Error No: 1001');
define('_MD_WFP_FILENOTFOUND', 'ERROR: Required file %s was not found in file: % at line %s');

define(
    '_MD_WFP_ERROR_MISSING_MODULE',
    '<b>Module Missing Warning!</b><br><br>
Why am I seeing this message rather than my expected module?<br><br>
Quite simply, you have not installed the required resource module for %s. Please goto https://wfprojects.x10hosting.com and download and install the latest version of WF-Resource (%s or higher)'
);
define(
    '_MD_WFP_ERROR_NOTACTIVE',
    '<b>Required Module Not Activated!</b><br><br>
Why am I seeing this message rather than my expected module?<br><br>
Quite Simple. You have installed WF-Resource, but the module has not been activated. WF-Resource must be activated for this module to work correctly.'
);
define(
    '_MD_WFP_ERROR_NOTUPDATE',
    '<b>WF-Resource Module Outdated!</b><br><br>
Why am I seeing this message rather than my expected module?<br><br>
Quite Simple. The Version of WF-Resource installed is out of date for this version of %s. <br>Please visit https://wfprojects.x10hosting.com, download and install WF-Resource ver %s or higher.'
);
define('_MD_WFP_TECHISSUES', '<b>Techincal issues</b><br><br>We are sorry, but we seem to be having some techincal issues with this part of our website.<br><br>Please report this issue to the webmaster.');
