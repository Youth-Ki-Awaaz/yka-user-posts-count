<?php

class YKA_UPC_SEARCH extends YKA_UPC_BASE{

	function __construct() {

		// SHORTCODE TO RENDER SEARCH FORM
		add_shortcode( 'yka_upc_search', array( $this, 'search_shortcode_cb') );

		// ADMIN AJAX FOR SEARCHING USERS BASED ON THE GIVEN POST_COUNT
		add_action( 'wp_ajax_yka_upc_search', array( $this, 'ajax_search_cb' ));

		// TO EXPORT DATA AS CSV
		add_action( 'wp_ajax_yka_upc_search_csv', array( $this, 'csv_cb' ) );

	}

	function search_shortcode_cb() {
		if( current_user_can('author') || current_user_can('administrator') ){
			ob_start();
			?>
			<h1>Search Author By Post Published Count</h1>
			<div style="margin-top: 20px;">
				<input type="number" name="upc-post-count" class="form-control" placeholder="Post Count" />
			  <button class="button upc-search-btn">Search</button>
			  <a class="button button-primary yka-upc-button csv-btn" style="display:none;" href="">Export as CSV</a>
				<div class="author-list-wrapper"><ul class="author-list"></ul></div>
			</div>
			<?php
	      	return ob_get_clean();
	    } else {
				echo '<div class="notice notice-error" style="text-align:center;padding: 10px 0;">UNAUTHORISED ACCESS</div>';
	    }
	}

	function ajax_search_cb(){

		if( ! isset( $_GET['upc-post-count'] ) ) {
			echo json_encode(array('error' => 'Insufficient Parameters'));
			wp_die();
		}

		global $wpdb;

		$offset = $_GET['offset'];
		$post_count = $_GET['upc-post-count'];
		$items_per_page = $_GET['items_per_page'];

		$query = "SELECT post_author, COUNT( * ) AS count FROM {$wpdb->posts}
		WHERE post_type='post' AND post_status='publish'
		GROUP BY post_author
		HAVING COUNT( * ) >= {$post_count}
		ORDER BY count DESC
		LIMIT $items_per_page OFFSET $offset";

		$rs = $wpdb->get_results( $query );

		//print_r( $rs );

		if( is_array(	$rs	) && count(	$rs ) > 0 ){

			$output = array();

			foreach( $rs as $key => $obj ){

				$auth_obj = get_user_by('ID', $obj->post_author);

				array_push( $output, array(
					'id' 					=> $auth_obj->ID,
					'name' 				=> $auth_obj->display_name,
					'email' => $auth_obj->user_email,
					'url'		=> get_author_posts_url( $auth_obj->ID )
				) );
			}

			$rs = $output;

		}

		echo json_encode($rs);

		wp_die();

	}

	function csv_cb() {

		$filename = 'yka-'.date('dmy-his').'.csv';
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-type: text/csv' );
		header( "Content-Disposition: attachment; filename=$filename" );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		$output = fopen("php://output", "w");

		global $wpdb;

		$post_count = $_GET['upc-post-count'];
		$column_names = array( 'ID', 'username', 'email', 'url', 'post_count' );

		$query = "SELECT post_author, COUNT( * ) AS count FROM {$wpdb->posts}
		WHERE post_type='post' AND post_status='publish'
		GROUP BY post_author
		HAVING COUNT( * ) >= {$post_count}
		ORDER BY count DESC";

		$rs = $wpdb->get_results( $query );

		fputcsv($output,$column_names);

		if( is_array($rs) && count($rs) > 0 ) {

			foreach ($rs as $key => $obj) {
				$auth_obj = get_user_by('ID', $obj->post_author);

				$data =  array(
					'id'					=> $auth_obj->ID,
					'name' 				=> $auth_obj->display_name,
					'email' 			=> $auth_obj->user_email,
					'url'					=> get_author_posts_url($auth_obj->ID),
					'post_count'	=> $obj->count
				);

				fputcsv($output, $data);
			}

		}

		fclose($output);

		die();

	}

}


YKA_UPC_SEARCH::getInstance();
