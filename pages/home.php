<?php
/**
 * Page Home
 */
defined("_ENTRY_") or die("Restricted Access!");

define("HOME_DEFAULT_VIEW", "hello");

import('core.site.Page');
import('core.site.Model');
import('core.blog.Blogset');

import("includes.debug");

$db = getDB();

// DB Query ////////////////////////////////////////////////////////////////////////////////////


// Data and Model //////////////////////////////////////////////////////////////////////////////

$model = new Model( 'Welcome to PHPorcupine');

$model->setView( get_var('view', 'get', HOME_DEFAULT_VIEW) );

// Page Controller /////////////////////////////////////////////////////////////////////////////

$page = new Page( SITE_NAME, &$model );
$page->setTemplate( get_var('template', 'get', 'default') );

$page->display();
?>