<?php


/**
 * Class Tribe__Tickets_Plus__Commerce__WooCommerce__Tabbed_View__Report_Tabbed_View
 *
 * Renders a tab navigation on top of a post attendance or sales report.
 *
 * The class is a convenience wrapper around the `Tribe__Tabbed_View` to set up and re-use
 * the code needed to set up and re-use this tabbed view.
 */
class Tribe__Tickets_Plus__Commerce__WooCommerce__Tabbed_View__Report_Tabbed_View {

	/**
	 * @var int The current post ID.
	 */
	protected $post_id;

	/**
	 * @var array A map that binds requested pages to tabs.
	 */
	protected $tab_map = array(
		'tickets-attendees' => 'tribe-tickets-attendance-report',
		'tickets-orders'    => 'tribe-tickets-plus-woocommerce-orders-report',
	);

	/**
	 * Tribe__Tickets_Plus__Commerce__WooCommerce__Tabbed_View__Report_Tabbed_View constructor.
	 *
	 * @param int $post_id The current post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Renders the tabbed view for the current post.
	 *
	 * The tabs are not AJAX powered by UI around existing links.
	 */
	public function render( $active = null ) {
		$view = new Tribe__Tabbed_View();
		$view->set_label( apply_filters( 'the_title', get_post( $this->post_id )->post_title ) );
		$query_string = empty( $_SERVER['QUERY_STRING'] ) ? '' : '?' . $_SERVER['QUERY_STRING'];
		$request_uri  = 'edit.php' . $query_string;
		$view->set_url( remove_query_arg( 'tab', $request_uri ) );

		if ( ! empty( $active ) ) {
			$view->set_active( $active );
		} else {
			// try to set the active tab from the requested page
			parse_str( $request_uri, $query_args );
			if ( ! empty( $query_args['page'] ) && isset( $this->tab_map[ $query_args['page'] ] ) ) {
				$active = $this->tab_map[ $query_args['page'] ];
				$view->set_active( $active );
			}
		}

		$attendees_report = new Tribe__Tickets__Tabbed_View__Attendee_Report_Tab( $view );
		$tickets_handler  = tribe( 'tickets.handler' );
		$post             = get_post( $this->post_id );
		$attendees_report->set_url( $tickets_handler->get_attendee_report_link( $post ) );
		$view->register( $attendees_report );

		$orders_report     = new Tribe__Tickets_Plus__Commerce__WooCommerce__Tabbed_View__Orders_Report_Tab( $view );
		$orders_report_url = Tribe__Tickets_Plus__Commerce__WooCommerce__Orders__Report::get_tickets_report_link( $post );
		$orders_report->set_url( $orders_report_url );
		$view->register( $orders_report );

		echo $view->render();
	}
}