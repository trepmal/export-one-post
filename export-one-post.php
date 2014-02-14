<?php
/*
 * Plugin Name: Export One Post
 * Plugin URI: trepmal.com
 * Description:
 * Version:
 * Author: Kailey Lampert
 * Author URI: kaileylampert.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * TextDomain: export-one-post
 * DomainPath:
 * Network:
 */

$export_one_post = new Export_One_Post();

class Export_One_Post {

	function __construct() {
		// due to a lack of hooks, we're using what we hope is an unlikely date match
		$this->future_date = '1970-01-05'; // Y-m-d

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_filter( 'export_args',                 array( $this, 'export_args' ) );
		add_filter( 'query',                       array( $this, 'query' ) );
	}

	function post_submitbox_misc_actions() {
		?>
		<style>
		#export-one-post:before {
			content: "\f316";
			color: #888;
			top: -1px;
			font: 400 20px/1 dashicons;
			speak: none;
			display: inline-block;
			padding: 0 2px 0 0;
			top: 0;
			left: -1px;
			position: relative;
			vertical-align: top;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			text-decoration: none!important;
		}
		</style>
		<div class="misc-pub-section">
			<span id="export-one-post">
				<a href="<?php echo esc_url( admin_url( 'export.php?download&post_ID='. get_the_ID() ) ); ?>"><?php _e( 'Export This', 'export-one-post' ); ?></a>
			</span>
		</div><?php
	}

	function export_args( $args ) {
		if ( ! isset( $_GET['post_ID'] ) ) return $args;

		$args['content']    = 'post';
		$args['start_date'] = $this->future_date;
		$args['end_date']   = $this->future_date;

		return $args;
	}

	function query( $query ) {
		if ( ! isset( $_GET['post_ID'] ) ) return $query;

		global $wpdb;

		// this is what WP will do by default
		// so if that's the query right now
		// then we need to swap it out
		$test = $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}  WHERE {$wpdb->posts}.post_type = 'post' AND {$wpdb->posts}.post_status != 'auto-draft' AND {$wpdb->posts}.post_date >= %s AND {$wpdb->posts}.post_date < %s",
			date( 'Y-m-d', strtotime( $this->future_date ) ),
			date( 'Y-m-d', strtotime('+1 month', strtotime( $this->future_date ) ) )
		);

		if ( $test != $query ) return $query;

		$split    = explode( 'WHERE', $query );
		$post_id  = intval( $_GET['post_ID'] );
		$split[1] = $wpdb->prepare( " {$wpdb->posts}.ID = %d", $post_id );
		$query    = implode( 'WHERE', $split );

		return $query;
	}

}