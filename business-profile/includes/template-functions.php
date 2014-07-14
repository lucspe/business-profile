<?php
/**
 * Template functions for rendering contact cards
 */

/**
 * Print a contact card and add a shortcode
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_contact_card' ) ) {
function bpwfwp_print_contact_card( $args = array() ) {

	// Define shortcode attributes
	$defaults = array(
		'show_name'					=> true,
		'show_address'				=> true,
		'show_get_directions'		=> true,
		'show_phone'				=> true,
		'show_contact'				=> true,
		'show_opening_hours'		=> true,
		'show_opening_hours_brief'	=> false,
		'show_map'					=> true,
	);

	$defaults = apply_filters( 'bpwfp_contact_card_defaults', $defaults );

	global $bpfwp_controller;
	$bpfwp_controller->display_settings = shortcode_atts( $defaults, $args, 'contact-card' );

	// Setup components and callback functions to render them
	$data = array();

	if ( $bpfwp_controller->settings->get_setting( 'name' ) ) {
		$data['name'] = 'bpwfwp_print_name';
	}

	if ( $bpfwp_controller->settings->get_setting( 'address' ) ) {
		$data['address'] = 'bpwfwp_print_address';
	}

	if ( $bpfwp_controller->settings->get_setting( 'phone' ) ) {
		$data['phone'] = 'bpwfwp_print_phone';
	}

	if ( $bpfwp_controller->display_settings['show_contact'] &&
			( $bpfwp_controller->settings->get_setting( 'contact-email' ) || $bpfwp_controller->settings->get_setting( 'contact-page' ) ) ) {
		$data['contact'] = 'bpwfwp_print_contact';
	}

	if ( $bpfwp_controller->settings->get_setting( 'opening-hours' ) ) {
		$data['opening_hours'] = 'bpwfwp_print_opening_hours';
	}

	if ( $bpfwp_controller->display_settings['show_map'] && $bpfwp_controller->settings->get_setting( 'address' ) ) {
		$data['map'] = 'bpwfwp_print_map';
	}

	$data = apply_filters( 'bpwfwp_component_callbacks', $data );


	if ( apply_filters( 'bpfwp-load-frontend-assets', true ) ) {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'bpfwp-default' );
	}

	ob_start();
	?>

	<address class="bp-contact-card" itemscope itemtype="http://schema.org/<?php echo $bpfwp_controller->settings->get_setting( 'schema_type' ); ?>">
		<?php foreach ( $data as $data => $callback ) { call_user_func( $callback ); } ?>
	</address>

	<?php
	$output = ob_get_clean();

	return apply_filters( 'bpwfwp_contact_card_output', $output );
}
if ( !shortcode_exists( 'contact-card' ) ) {
	add_shortcode( 'contact-card', 'bpwfwp_print_contact_card' );
}
} // endif;

/**
 * Print the name
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_name' ) ) {
function bpwfwp_print_name() {

	global $bpfwp_controller;

	if ( $bpfwp_controller->display_settings['show_name'] ) :
	?>
	<div class="bp-name" itemprop="name">
		<?php echo esc_attr( $bpfwp_controller->settings->get_setting( 'name' ) ); ?>
	</div>

	<?php else : ?>
	<meta itemprop="name" content="<?php echo esc_attr( $bpfwp_controller->settings->get_setting( 'name' ) ); ?>">

	<?php endif; ?>

	<meta itemprop="description" content="<?php echo esc_attr( get_bloginfo( 'url' ) ) ?>">
	<meta itemprop="url" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">

	<?php
}
} // endif;

/**
 * Print the address with a get directions link to Google Maps
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_address' ) ) {
function bpwfwp_print_address() {

	global $bpfwp_controller;

	$address = $bpfwp_controller->settings->get_setting( 'address' );

	if ( $bpfwp_controller->display_settings['show_address'] ) :
	?>

	<div class="bp-address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<?php echo nl2br( $address['text'] ); ?>
	</div>

	<?php else : ?>
	<meta itemprop="address" content="<?php echo esc_attr( $address['text'] ); ?>">

	<?php endif; ?>

	<?php if ( $bpfwp_controller->display_settings['show_get_directions'] ) : ?>
	<div class="bp-directions">
		<a href="//maps.google.com/maps?saddr=current+location&daddr=<?php echo urlencode( esc_attr( $address['text'] ) ); ?>"><?php _e( 'Get directions', BPFWP_TEXTDOMAIN ); ?></a>
	</div>
	<?php endif;

}
} // endif;

/**
 * Print the phone number
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_phone' ) ) {
function bpwfwp_print_phone() {

	global $bpfwp_controller;

	if ( $bpfwp_controller->display_settings['show_phone'] ) :
	?>

	<div class="bp-phone" itemprop="telephone">
		<?php echo $bpfwp_controller->settings->get_setting( 'phone' ); ?>
	</div>

	<?php else : ?>
	<meta itemprop="telephone" content="<?php echo esc_attr( $bpfwp_controller->settings->get_setting( 'phone' ) ); ?>">

	<?php endif;
}
} // endif;

/**
 * Print the contact link
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_contact' ) ) {
function bpwfwp_print_contact() {

	global $bpfwp_controller;

	$email = $bpfwp_controller->settings->get_setting( 'contact-email' );
	if ( !empty( $email ) ) :
	?>

	<div class="bp-contact bp-contact-email" itemprop="email">
		<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo $email; ?></a>
	</div>

	<?php
		return;
	endif;

	$contact = $bpfwp_controller->settings->get_setting( 'contact-page' );
	if ( !empty( $contact ) ) :
	?>

	<div class="bp-contact bp-contact-page" itemprop="ContactPoint" itemscope itemtype="http://schema.org/ContactPoint">
		<a href="<?php echo get_post_permalink( $contact ); ?>"><?php _e( 'Contact', BPFWP_TEXTDOMAIN ); ?></a>
	</div>

	<?php endif;

}
} // endif;

/**
 * Print the opening hours
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_opening_hours' ) ) {
function bpwfwp_print_opening_hours() {

	global $bpfwp_controller;

	$weekdays_schema = array(
		'monday'	=> 'Mo',
		'tuesday'	=> 'Tu',
		'wednesday'	=> 'We',
		'thursday'	=> 'Th',
		'friday'	=> 'Fr',
		'saturday'	=> 'Sa',
		'sunday'	=> 'Su',
	);

	$hours = $bpfwp_controller->settings->get_setting( 'opening-hours' );

	// Output proper schema.org format
	foreach( $hours as $slot ) {

		// Skip this entry if no weekdays are set
		if ( empty( $slot['weekdays'] ) ) {
			continue;
		}

		$days = array();
		foreach( $slot['weekdays'] as $day => $val ) {
			$days[] = $weekdays_schema[ $day ];
		}
		$string = !empty( $days ) ? join( ',', $days ) : '';

		if ( !empty( $string) && !empty( $slot['time'] ) ) {

			if ( empty( $slot['time']['start'] ) ) {
				$start = '00:00';
			} else {
				$start = trim( substr( $slot['time']['start'], 0, -2 ) );
				if ( substr( $slot['time']['start'], -2 ) == 'PM' && $start !== '12:00' ) {
					$split = explode( ':', $start );
					$split[0] += 12;
					$start = join( ':', $split );
				}
				if ( substr( $slot['time']['start'], -2 ) == 'AM' && $start == '12:00' ) {
					$start = '00:00';
				}
			}

			if ( empty( $slot['time']['end'] ) ) {
				$end = '24:00';
			} else {
				$end = trim( substr( $slot['time']['end'], 0, -2 ) );
				if ( substr( $slot['time']['end'], -2 ) == 'PM' ) {
					$split = explode( ':', $end );
					$split[0] += 12;
					$end = join( ':', $split );
				}
				if ( substr( $slot['time']['start'], -2 ) == 'AM' && $start == '12:00' ) {
					$end = '24:00';
				}
			}

			$string .= ' ' . $start . '-' . $end;
		}
		echo '<meta itemprop="openingHours" content="' . esc_attr( $string ) . '">';
	}

	// Output display format
	if ( !$bpfwp_controller->display_settings['show_opening_hours'] ) {
		return;
	}

	if ( $bpfwp_controller->display_settings['show_opening_hours_brief'] ) :
	?>

	<div class="bp-opening-hours-brief">

	<?php
		$slots = array();
		foreach( $hours as $slot ) {

			// Skip this entry if no weekdays are set
			if ( empty( $slot['weekdays'] ) ) {
				continue;
			}

			$days = array();
			foreach( $slot['weekdays'] as $day => $val ) {
				$days[] = $weekdays_schema[ $day ];
			}
			$string = !empty( $days ) ? join( ',', $days ) : '';


			if ( empty( $slot['time'] ) ) {
				$string .= __( ' all day', BPFWP_TEXTDOMAIN );
			} else {
				if ( !empty( $slot['time']['start'] ) ) {
					$start = new DateTime( $slot['time']['start'] );
				}
				if ( !empty( $slot['time']['end'] ) ) {
					$end = new DateTime( $slot['time']['end'] );
				}

				if ( empty( $start ) ) {
					$string .= __( ' open until ', BPFWP_TEXTDOMAIN ) . $end->format( get_option( 'time_format' ) );
				} elseif ( empty( $end ) ) {
					$string .= __( ' open from ', BPFWP_TEXTDOMAIN ) . $start->format( get_option( 'time_format' ) );
				} else {
					$string .= ' ' . $start->format( get_option( 'time_format' ) ) . _x( '-', 'Separator between opening and closing times. Example: 9:00am-5:00pm', BPFWP_TEXTDOMAIN ) . $end->format( get_option( 'time_format' ) );
				}
			}

			$slots[] = $string;
		}

		echo join( _x( '; ', 'Separator between multiple opening times in the brief opening hours. Example: Mo,We 9:00 AM - 5:00 PM; Tu,Th 10:00 AM - 5:00 PM', BPFWP_TEXTDOMAIN ), $slots );
	?>

	</div>

	<?php
		return;
	endif; // brief opening hours

	$weekdays_display = array(
		'monday'	=> __( 'Monday' ),
		'tuesday'	=> __( 'Tuesday' ),
		'wednesday'	=> __( 'Wednesday' ),
		'thursday'	=> __( 'Thursday' ),
		'friday'	=> __( 'Friday' ),
		'saturday'	=> __( 'Saturday' ),
		'sunday'	=> __( 'Sunday' ),
	);

	$weekdays = array();
	foreach( $hours as $rule ) {

		// Skip this entry if no weekdays are set
		if ( empty( $rule['weekdays'] ) ) {
			continue;
		}

		if ( empty( $rule['time'] ) ) {
			$time = __( 'Open', BPFWP_TEXTDOMAIN );

		} else {

			if ( !empty( $rule['time']['start'] ) ) {
				$start = new DateTime( $rule['time']['start'] );
			}
			if ( !empty( $rule['time']['end'] ) ) {
				$end = new DateTime( $rule['time']['end'] );
			}

			if ( empty( $start ) ) {
				$time = __( 'Open until ', BPFWP_TEXTDOMAIN ) . $end->format( get_option( 'time_format' ) );
			} elseif ( empty( $end ) ) {
				$time = __( 'Open from ', BPFWP_TEXTDOMAIN ) . $start->format( get_option( 'time_format' ) );
			} else {
				$time = $start->format( get_option( 'time_format' ) ) . _x( '-', 'Separator between opening and closing times. Example: 9:00am-5:00pm', BPFWP_TEXTDOMAIN ) . $end->format( get_option( 'time_format' ) );
			}
		}

		foreach( $rule['weekdays'] as $day => $val ) {

			if ( !array_key_exists( $day, $weekdays ) ) {
				$weekdays[$day] = array();
			}

			$weekdays[$day][] = $time;
		}
	}

	if ( count( $weekdays ) ) :

		// Order the weekdays and add any missing days as "closed"
		$weekdays_ordered = array();
		foreach( $weekdays_display as $slug => $name ) {
			if ( !array_key_exists( $slug, $weekdays ) ) {
				$weekdays_ordered[$slug] = array( __( 'Closed', BPFWP_TEXTDOMAIN ) );
			} else {
				$weekdays_ordered[$slug] = $weekdays[$slug];
			}
		}
	?>

	<div class="bp-opening-hours">
		<span class="bp-title"><?php _e( 'Opening Hours', BPFWP_TEXTDOMAIN ); ?></span>
		<?php foreach ( $weekdays_ordered as $weekday => $times ) :	?>
		<div class="bp-weekday">
			<span class="bp-weekday-name bp-weekday-<?php echo $weekday; ?>"><?php echo $weekdays_display[$weekday]; ?></span>
			<span class="bp-times">
			<?php foreach ( $times as $time ) : ?>
				<span class="bp-time"><?php echo $time; ?></span>
			<?php endforeach; ?>
			</span>
		</div>
		<?php endforeach; ?>
	</div>

	<?php
	endif;
}
} // endif;

/**
 * Print a map to the address
 * @since 0.0.1
 */
if ( !function_exists( 'bpwfwp_print_map' ) ) {
function bpwfwp_print_map() {

	global $bpfwp_controller;

	$address = $bpfwp_controller->settings->get_setting( 'address' );

	wp_enqueue_script( 'bpfwp-map' );
	wp_localize_script(
		'bpfwp-map',
		'bpfwp_map',
		array(
			'strings' => array(
				'get_directions' => __( 'Get directions', BPFWP_TEXTDOMAIN ),
			)
		)
	);

	global $bpfwp_map_ids;
	if ( empty( $bpfwp_map_ids ) ) {
		$bpfwp_map_ids = array();
	}

	$id = count( $bpfwp_map_ids );
	$bpfwp_map_ids[] = $id;


	$attr = '';

	$phone = $bpfwp_controller->settings->get_setting( 'phone' );
	if ( !empty( $phone ) ) {
		$attr .= ' data-phone="' . esc_attr( $phone ) . '"';
	}

	if ( !empty( $address['lat'] ) && !empty( $address['lon'] ) ) {
		$attr .= ' data-lat="' . esc_attr( $address['lat'] ) . '" data-lon="' . esc_attr( $address['lon'] ) . '"';
	}
	?>

	<div id="bp-map-<?php echo $id; ?>" class="bp-map" itemprop="map" data-name="<?php echo esc_attr( $bpfwp_controller->settings->get_setting( 'name' ) ); ?>" data-address="<?php echo esc_attr( nl2br( $address['text'] ) ); ?>"<?php echo $attr; ?>>
	</div>

	<?php
}
} // endif;
