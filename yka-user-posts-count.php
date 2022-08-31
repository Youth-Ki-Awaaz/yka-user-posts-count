<?php
/*
Plugin Name: YKA User Posts Count
Plugin URI: Plugin URI: https://sputznik.com
Description: Plugin to show list of users who have published posts
Version: 1.0.0
Author: Stephen Anil (Sputznik)
Author URI: https://sputznik.com
*/

if( ! defined( 'ABSPATH' ) ){ exit; }

define( 'YKA_UPC_URI', plugin_dir_url( __DIR__ ).'yka-user-posts-count/' ); // GIVES THE ROOT URL OF THE PLUGIN

$inc_files = array(
  'class-yka-upc-base.php',
  'includes/admin/class-yka-upc-admin.php',
  'includes/class-yka-upc-search.php'
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}
