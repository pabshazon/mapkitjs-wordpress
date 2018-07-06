<?php
/**
 * @package Thehumsum-Pledges
 */
/*
Plugin Name: Thehumsum - Pledges plugin
Plugin URI: https://www.thehumsum.org
Description: This plugin creates the pledges section for The Hum Sum site
Version: 1.0.0
Author: Pablo "Nomad" Ferrari
Author URI: http://nomads.ai
License: GPLv2
Text-domain: thehumsum-pledges
 */

defined ('ABSPATH') or die('Permission denied');
define('PLEDGES_PATH', plugin_dir_path(__FILE__));

include(PLEDGES_PATH . 'controllers/pledgesController.php');

/**
 * PledgesController instantiation, as the main and only plugin actions controller.
 * Notes for reusability: In pledges there is no need to abstract into an actionDispatcher or ActionRouter, just
 * one controller, no pre/post dispatch needs.
 */
$pledgesController = new PledgesController();
add_action('init', array($pledgesController, '__construct'));



/**
 * WP Plugin lifecycle hooks - activation, deactivation, uninstall
 */
register_activation_hook( __FILE__, 'humsum_pledges_activate' );
function humsum_pledges_activate(){
    //create db and register activation hooks when plugin is activated
    require_once(PLEDGES_PATH . 'models/pledgeModel.php');
    $pledgeModel = new PledgeModel;
    $pledgeModel->createDatabase();

}

register_deactivation_hook( __FILE__, 'humsum_pledges_deactivate' );
function humsum_pledges_deactivate(){
    //register deactivation hooks when plugin is deactivated

}

register_uninstall_hook( __FILE__, 'humsum_pledges_deactivate' );
function humsum_pledges_uninstall(){
    //@todo: decide if we want to delete the db table when plugin is uninstalled

}