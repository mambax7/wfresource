<?php declare(strict_types=1);
/**
 * Module: WF-Channel
 * Version: v1.0.2
 * Release Date: 20 November 2003
 * Author: Catzwolf
 * Licence: GNU
 */

// Module Info
// The name of this module
define('_MD_WF_RESOURCE', 'WF-Resource');
// A brief description of this module
define('_MD_WF_RESOURCE_DSC', 'This module is REQUIRED for the functionality of all WF-Project modules.');
/**
 * Print Page
 */

//Help
define('_MI_WF_RESOURCE_DIRNAME', basename(dirname(__DIR__, 2)));
define('_MI_WF_RESOURCE_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_WF_RESOURCE_BACK_2_ADMIN', 'Back to Administration of ');
define('_MI_WF_RESOURCE_OVERVIEW', 'Overview');

//define('_MI_WF_RESOURCE_HELP_DIR', __DIR__);

//help multipage
define('_MI_WF_RESOURCE_DISCLAIMER', 'Disclaimer');
define('_MI_WF_RESOURCE_LICENSE', 'License');
define('_MI_WF_RESOURCE_SUPPORT', 'Support');
