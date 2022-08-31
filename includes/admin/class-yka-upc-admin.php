<?php

class YKA_UPC_ADMIN extends YKA_UPC_BASE {

  function __construct(){
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );

  }

  function assets(){
    wp_enqueue_script( 'upc-search', YKA_UPC_URI . 'includes/js/upc_search.js', array('jquery'), time(), true );
  }

  function admin_menu(){
    add_menu_page(
      'YKA UPC',
      'YKA UPC',
      'edit_posts',
      'yka-upc-admin',
      array( $this, 'menu_page' ),
      'dashicons-media-interactive'
    );
	}

  function menu_page(){
    include( 'templates/admin.php' );
  }

}

YKA_UPC_ADMIN::getInstance();
