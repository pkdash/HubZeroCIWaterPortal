<?php
/**
* @version		$Id: framework.php 22952 2012-03-27 00:40:16Z dextercowley $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2012 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * Joomla! system checks
 */

@ini_set('zend.ze1_compatibility_mode', '0');

/*
 * Installation check, and check on removal of the install directory.
 */
if (!file_exists( JPATH_CONFIGURATION . DS . 'configuration.php' ) ) {
	die('Error - Configuration file does not exist');
}

/*
 * Joomla! system startup
 */

// System includes
require_once( JPATH_LIBRARIES		.DS.'joomla'.DS.'import.php');

// Pre-Load configuration
require_once( JPATH_CONFIGURATION	.DS.'configuration.php' );

if (!class_exists('JConfig'))
{
	die('Error - Invalid Configuration File');
}

// System configuration
$CONFIG = new JConfig();

/* if configuration just has an install key and no other properties then redirect into the installer */

if (count(get_object_vars($CONFIG)) <= 1)
{
	if( file_exists( JPATH_INSTALLATION . DS . 'index.php' ) ) {
		header( 'Location: installation/index.php' );
		exit();
	} else {
		echo 'Installation requested by configuration but no installation code available. Exiting...';
		exit();
	}
}

if (@$CONFIG->error_reporting === 0) {
	error_reporting( 0 );
} else if (@$CONFIG->error_reporting > 0) {
	error_reporting( $CONFIG->error_reporting );
	ini_set( 'display_errors', 1 );
}

define( 'JDEBUG', $CONFIG->debug );


unset( $CONFIG );

/*
 * Joomla! framework loading
 */

// Include object abstract class
require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'compat'.DS.'compat.php');

// System profiler
if (JDEBUG) {
	jimport( 'joomla.error.profiler' );
	$_PROFILER =& JProfiler::getInstance( 'Application' );
}

// Joomla! library imports
jimport( 'joomla.application.menu' );
jimport( 'joomla.user.user');
jimport( 'joomla.environment.uri' );
jimport( 'joomla.html.html' );
jimport( 'joomla.html.parameter' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.event.event');
jimport( 'joomla.event.dispatcher');
jimport( 'joomla.language.language');
jimport( 'joomla.utilities.string' );
?>
