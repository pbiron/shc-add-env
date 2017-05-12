<?php
/*
 * Plugin Name: Add Environment to Admin Bar
 * Description: Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Dev, Local, etc)
 * Version: 0.1
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com
 * Text Domain: shc-add-env
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright 2017 Paul V. Biron/Sparrow Hawk Computing
 */

add_action ('init', 'shc_add_env_init') ;

/**
 * Add appropriate actions
 *
 * @return void
 *
 * @action init
 */
function
shc_add_env_init ()
{
	// load our textdomain
	load_plugin_textdomain ('shc-add-env', false, dirname (plugin_basename (__FILE__)) . '/languages') ;

	// add our node to the admin bar
	// priority=1 is to get it as far to the right as possible
	add_action ('admin_bar_menu', 'shc_add_env_add_node', 1) ;

	if (is_admin ()) {
		add_action ('admin_enqueue_scripts', 'shc_add_env_enqueue') ;
		}
	else if (is_admin_bar_showing ()) {
		add_action ('wp_enqueue_scripts', 'shc_add_env_enqueue') ;
		}

	return ;
}

/**
 * Add a node to the admin bar that identifies the environment
 *
 * @return void
 *
 * @action admin_bar_menu
 */
function
shc_add_env_add_node ()
{
	global $wp_admin_bar ;

	$env = shc_add_env_get_env () ;

	$class = str_replace (' ', '-', strtolower ($env)) ;
	$args = array (
		'id' => "shc-add-env",
		'parent' => 'top-secondary',
		'meta'   => array ('class' => "shc-add-env $class"),
		'title' => $env,
		) ;
	$wp_admin_bar->add_node ($args) ;

	return ;
}

/**
 * Identify the environment
 *
 * If the SERVER_ADDR is a loopback or LAN IP address or SERVER_NAME is 'localhost', then 'Local';
 * else if WP_DEBUG is true, then 'Dev'; else 'Prod'.
 *
 * The value determined above is then filtered with the 'shc_add_env_get_env' filter,
 * by which other plugins could 
 *
 * @return string
 */
function
shc_add_env_get_env ()
{
	/* translators: abbreviation for 'Production' */
	$env = __('Prod', 'shc-add-env') ;

	if (preg_match ('/^(127|192\.168|169\.254)\./', $_SERVER['SERVER_ADDR']) || 'localhost' === $_SERVER['SERVER_NAME']) {
		/* translators: abbreviation for 'Localhost' */
		$env = __('Local', 'shc-add-env') ;
		}
	else if (defined ('WP_DEBUG') && WP_DEBUG) {
		/* translators: abbreviation for 'Development' */
		$env = __('Dev', 'shc-add-env') ;
		}

	/**
	 * Filter the value of the environment
	 *
	 * Those who hook into this filter and return a value other than 'Prod', 'Dev' or 'Local'
	 * should also enqueue a stylesheet that defines the background color for their return value.
	 *
	 * For example, if the hooked function could return 'QA' or 'My Environment', they should
	 * add the styles such as:
	 *
	 * #wpadminbar .ab-top-menu .shc-add-env.qa .ab-item,
	 * #wpadminbar .ab-top-menu .shc-add-env.qa:hover .ab-item
	 * {
	 *		background-color: #523f6d ;
	 * }
	 * 
	 * #wpadminbar .ab-top-menu .shc-add-env.my-environment .ab-item,
	 * #wpadminbar .ab-top-menu .shc-add-env.my-environment:hover .ab-item
	 * {
	 *		background-color: #04a4cc ;
	 * }
	 *
	 * In such cases, care should be taken to select colors that have sufficient
	 * contrast to the background-color of the admin bar for each of the Admin Color
	 * Scheme's shipped with WordPress...and sufficient contrast can't be guaranteed,
	 * the add additional styling (e.g., by adding a colored border) the help the
	 * node stand out. See the '.admin-color-sunrise #wpadminbar .ab-top-menu .shc-add-env.prod .ab-item'
	 * rule for an example.
	 *
	 * @param string $env The environment
	 *
	 * @return string
	 */
	return (apply_filters ('shc_add_env_get_env', $env)) ;
}

/**
 * Enqueue our stylesheet
 *
 * @return void
 *
 * @action admin_enqueue_scripts, wp_enqueue_scripts
 */
function
shc_add_env_enqueue ()
{
	wp_enqueue_style ('shc_add_env', plugins_url ('css/styles.css', __FILE__)) ;
	
	return ;
}
