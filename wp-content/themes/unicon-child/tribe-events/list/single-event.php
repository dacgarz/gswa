<?php
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/single-event.php
 *
 * @package TribeEventsCalendar
 * @version  4.3
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Setup an array of venue details for use later in the template
$venue_details = tribe_get_venue_details();

// Venue
$has_venue_address = ( ! empty( $venue_details['address'] ) ) ? ' location' : '';

// Organizer
$organizer = tribe_get_organizer();

?>
<?php if (($event_image = tribe_event_featured_image( null, 'medium' )) != NULL): ?>
	<div class="event-image-wrapper">
		<!-- Event Image -->
		<?php echo  $event_image?>
	</div>
<?php endif; ?>
<div class="event-content-wrapper <?php if (empty($event_image)) print 'event-without-image'; ?>">
	<!-- Schedule & Recurrence Details -->
	<div class="tribe-event-schedule-details">
		<?php echo tribe_events_event_schedule_details() ?>
	</div>

	<!-- Event Cost -->
	<?php if ( tribe_get_cost() ) : ?>
		<div class="tribe-events-event-cost">
			<span><?php echo tribe_get_cost( null, true ); ?></span>
		</div>
	<?php endif; ?>

	<!-- Event Title -->
	<?php do_action( 'tribe_events_before_the_event_title' ) ?>
	<h2 class="tribe-events-list-event-title">
		<a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title_attribute() ?>" rel="bookmark">
			<?php the_title() ?>
		</a>
	</h2>
	<?php do_action( 'tribe_events_after_the_event_title' ) ?>

	<!-- Event Meta -->
	<?php do_action( 'tribe_events_before_the_meta' ) ?>
	<?php if ( $venue_details ) : ?>
		<div class="tribe-events-event-meta">
			<div class="author <?php echo esc_attr( $has_venue_address ); ?>">
				<!-- Venue Display Info -->
				<div class="tribe-events-venue-details">
					<?php echo implode( ', ', $venue_details ); ?>
					<?php
					if ( tribe_get_map_link() ) {
						echo tribe_get_map_link_html();
					}
					?>
				</div> <!-- .tribe-events-venue-details -->
			</div>
		</div><!-- .tribe-events-event-meta -->
	<?php endif; ?>
	<?php do_action( 'tribe_events_after_the_meta' ) ?>

	<!-- Event Content -->
	<?php do_action( 'tribe_events_before_the_content' ) ?>
	<div class="tribe-events-list-event-description tribe-events-content">
		<?php echo custom_tribe_events_get_the_excerpt( null, wp_kses_allowed_html( 'post' ), ($venue_details ? 20 : 35) ); ?>
	</div><!-- .tribe-events-list-event-description -->
</div>
<?php
do_action( 'tribe_events_after_the_content' );
