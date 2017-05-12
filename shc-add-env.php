<?php
/*
 * Plugin Name: Add Environment to Admin Bar
 * Description: Add an indication to the Admin Bar of the environment WordPress is running in (e.g., Prod, Dev, Local, etc)
 * Version: 0.1.1
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com
 * Text Domain: shc-add-env
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/pbiron/shc-add-env
 * GitHub Branch: dev
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

	$class = str_replace (' ', '-', $env['class']) ;
	$args = array (
		'id' => "shc-add-env",
		'parent' => 'top-secondary',
		'meta'   => array ('class' => "shc-add-env $class"),
		'title' => $env['title'],
		) ;
	$wp_admin_bar->add_node ($args) ;

	return ;
}

/**
 * Identify the environment
 *
 * @return string
 *
 * @todo write a better description
 */
function
shc_add_env_get_env ()
{
	// if one of our constants is defined, return that...without the possiblity to
	// filter it
	if (defined ('SHC_ADD_ENV_PROD')) {
		return (array ('title' => SHC_ADD_ENV_PROD, 'class' => 'prod')) ;
		}
	else if (defined ('SHC_ADD_ENV_STAGING')) {
		return (array ('title' => SHC_ADD_ENV_STAGING, 'class' => 'staging')) ;
		}
	else if (defined ('SHC_ADD_ENV_DEV')) {
		return (array ('title' => SHC_ADD_ENV_DEV, 'class' => 'dev')) ;
		}

	/* translators: abbreviation for 'Production' */
	$env = array ('title' => __('Prod', 'shc-add-env'), 'class' => 'prod') ;

	if (preg_match ('/^(127|192\.168|169\.254)\./', $_SERVER['SERVER_ADDR']) || 'localhost' === $_SERVER['SERVER_NAME']) {
		/* translators: abbreviation for 'Localhost' */
		$env = array ('title' => __('Local', 'shc-add-env'), 'class' => 'dev') ;
		}
	else if (defined ('WP_DEBUG') && WP_DEBUG) {
		/* translators: abbreviation for 'Development' */
		$env = array ('title' => __('Dev', 'shc-add-env'), 'class' => 'dev') ;
		}
	// @todo figured out a way to detect a staging env

	/**
	 * Filter the value of the environment
	 *
	 * @param array $env {
	 *     $title The title to show in the Admin Bar
	 *     $class The class of the env.  One of 'prod', 'staging', 'dev'
	 * }
	 *
	 * @return array $env {
	 *     $title The title to show in the Admin Bar
	 *     $class The class of the env.  One of 'prod', 'staging', 'dev'
	 * }
	 *
	 * @todo add better descriptions for @param and @return, including how to define new classes
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
