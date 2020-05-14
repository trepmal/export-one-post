<?php
/**
 * Export One Post
 *
 * Class for namespacing purposes
 *
 */
class Export_One_Post {

	/**
	 * Get hooked in
	 * Define var
	 *
	 */
	function __construct() {
		// due to a lack of hooks, we're using what we hope is an unlikely date match
		$this->fake_date = '1970-01-05'; // Y-m-d

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Get hooked in: Part II
	 *
	 */
	function init() {
		if ( current_user_can( 'export' ) ) {
			add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
			add_filter( 'export_args',                 array( $this, 'export_args' ) );
			add_filter( 'query',                       array( $this, 'query' ) );
		}
	}
	/**
	 * Insert our action link into the submit box
	 *
	 */
	function post_submitbox_misc_actions() {
		?>
		<style>
		.export-one-post:before {
			content: "\f316";
			color: #82878c;
			font: normal 20px/1 dashicons;
			speak: none;
			display: inline-block;
			padding: 0 3px 0 0;
			vertical-align: top;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		</style>
		<div class="misc-pub-section export-one-post">
			<?php
				$export_url = add_query_arg( array(
					'download'      => '',
					'export_single' => get_the_ID(),
				), admin_url( 'export.php' ) );
			?>
			<a href="<?php echo esc_url( $export_url ); ?>"><?php esc_html_e( 'Export This', 'export-one-post' ); ?></a>
		</div><?php
	}

	/**
	 * Modify export arguments
	 * except if normal export
	 *
	 * @param array $args Query args for determining what should be exported
	 * @return $args Modified query
	 */
	function export_args( $args ) {
		// if no export_single var, it's a normal export - don't interfere
		if ( ! isset( $_GET['export_single'] ) ) {
			return $args;
		}

		// use our fake date so the query is easy to find (because we don't have a good hook to use)
		$args['content']    = 'post';
		$args['start_date'] = $this->fake_date;
		$args['end_date']   = $this->fake_date;

		return $args;
	}

	/**
	 * Filter query
	 * Look for 'tagged' query, replace with one matching the needs
	 *
	 * @param string $query SQL query
	 * @return string Modified SQL query
	 */
	function query( $query ) {
		if ( ! isset( $_GET['export_single'] ) ) {
			return $query;
		}

		global $wpdb;

		// This is the query WP will build (given our arg filtering above)
		// Since the current_filter isn't narrow, we'll check each query
		// to see if it matches, then if it is we replace it
		// @see https://github.com/wordpress/wordpress/blob/5.4.1/wp-admin/includes/export.php#L144
		$test = $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}  WHERE {$wpdb->posts}.post_type = 'post' AND {$wpdb->posts}.post_status != 'auto-draft' AND {$wpdb->posts}.post_date >= %s AND {$wpdb->posts}.post_date < %s",
			date( 'Y-m-d', strtotime( $this->fake_date ) ),
			date( 'Y-m-d', strtotime( '+1 month', strtotime( $this->fake_date ) ) )
		);

		if ( $test !== $query ) {
			return $query;
		}

		// divide query
		$split    = explode( 'WHERE', $query );
		// replace WHERE clause
		$split[1] = $wpdb->prepare( " {$wpdb->posts}.ID = %d", intval( $_GET['export_single'] ) );
		// put query back together
		$query    = implode( 'WHERE', $split );

		return $query;
	}

}
