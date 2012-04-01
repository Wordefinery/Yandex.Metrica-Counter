<?php
/**
 * @package Wordefinery Yandex.Metrica Counter
 */
/*
Plugin Name: Wordefinery Yandex.Metrica Counter
Plugin URI: http://wordefinery.com/plugins/metrica-counter/?from=wp&v=0.6.8.1
Description: Displays Yandex.Metrica counter
Version: 0.6.8.1
Author: Wordefinery
Author URI: http://wordefinery.com
License: GPLv2 or later

*/

if ( !function_exists( 'add_action' ) ) {
    echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
    exit;
}

require_once(dirname( __FILE__ ) . '/lib/init.php');
Wordefinery::Register(dirname( __FILE__ ), 'YandexmetricaCounter', '0.6.8.1');
