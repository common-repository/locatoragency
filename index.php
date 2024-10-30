<?php
/**
 * Index for execute the fonctionality
 * @category Wordpress
 * @copyright Copyright &copy; 2013, Amine BETARI (email: amine.betari@gmail.com)
 * @author abetari
 */
/*
Plugin Name: LocatorAgency
Description: System for managing locations, add, update and delete location. the ShortCode 'LOCATOR' must to be added in page for displayin a map.
Version: 1.0
Author: Amine BETARI, Agency SQLI-OUJDA
Author URI: http://www.abetari.com
License: GPL2
*/

// Different Constante of Plugin 'LocatorAgency'
define('PLUGIN_PATH_LOCATOR',str_replace('\\','/',dirname(__FILE__)));
define('ICONE_LOCATOR', plugins_url('locator.png', __FILE__));
define('LANGUAGE_PATH_LOCATOR', dirname( plugin_basename( __FILE__ ) ) .'/languages/' );
// TextDomain for tradution
define('LOCATOR', 'agency-locator');
require_once  PLUGIN_PATH_LOCATOR.'/classes/postType.php';
require_once  PLUGIN_PATH_LOCATOR.'/classes/Locator.php';

// New Custom Post Type : lexique
$locator = 'Locator';
$locators = 'Locators';
if(get_option('agency_locator_singular')){
	$locator = get_option('agency_locator_singular');
}

if(get_option('agency_locator_plural')){
	$locators = get_option('agency_locator_plural');
}

$postType = 'locator_agency';
new PostType($postType,
				   array('singular' => $locator,
                         'plural' => $locators,
                         'slug' => $postType,
                         'menu_icon' => ICONE_LOCATOR,
                         'args' => array('supports' => array('title', 'editor', 'excerpt', 'thumbnail'))
                         ));

new Locator();
