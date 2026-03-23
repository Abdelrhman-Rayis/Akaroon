<?php

defined( 'ABSPATH' )or die( 'Stop! You can not do this!' );

function salat_times_options_page() {
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Salat Times Settings</h1><a href="#help" class="page-title-action">How to use?</a>
		<hr class="wp-header-end">
		<?php

		if ( isset( $_POST[ "restore_defaults" ] ) == "1" ) {
			delete_option( 'st_options' );
		}

		?>

		<form id="auto_options" method="post" action="options.php">

			<?php

			if (isset($_POST['form_submitted'])) {
				$sanitized_options = array();
				$st_options = $_POST['st_options'];
				foreach($st_options as $key=>$value) {
					$sanitized_options[$key] = sanitize_text_field($value);
				}
				update_option("st_options", $sanitized_options);

				?>
				<div class="notice notice-success is-dismissible">
					<p><?php _e( 'Settings Saved.', 'salat-times' ); ?></p>
				</div>
				<?php
			}

			settings_fields( 'salat-times-settings-group' );

			$defaults = array(
				'lat_long_tz' => '23.7 90.4 6',
				'lat' => '23.7',
				'long' => '90.4',
				'custom_loc' => '0',
				'calc_method' => '1',
				'asr_method' => '0',
				'highlats' => '0',
				'time_format' => '1',
				'time_zone' => '6',
				'daylight' => '0',
				'wgt_title1' => 'Salat Times',
				'location' => 'Dhaka, Bangladesh',
				'show_date' => '1',
				'show_hdate' => '0',
				'hijri_adjust' => '-0',
				'dir' => 'inherit',
				'width' => '100%',
				'halign' => 'center',
				'talign' => 'center',
				'walign' => 'left',
				'scheme' => '#4189dd #ffffff #4472C4 #ffffff #B4C6E7 #D9E2F3 #000000',
				'custom' => 'Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah',
				'lang' => 'en',
				'timetable' => '0'
			);

			$st_options = get_option( "st_options" );
			if ( !is_array( $st_options ) ) {
				$st_options = $defaults;
			} else {
				foreach ( $defaults as $key => $value ) {
					if ( !array_key_exists( $key, $st_options ) ) {
						$st_options[ $key ] = $value;
					}
				}
			}
			?>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label>Timetable Type</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Timetable Types</span></legend>
								<?php
									$ttypes = array(
										'0' => 'Automatic Calculation',
										'1' => 'Manual Input',
									);
									foreach($ttypes as $key=>$value) {
										$checked = $st_options['timetable'] == $key ? ' checked' : '';
										echo '<label for="ttypes'.esc_html($key).'"><input onClick="changeTimetable('.esc_html($key).')" id="ttypes'.esc_html($key).'" type="radio" name="st_options[timetable]" value="'.esc_html($key).'"'.$checked.'>'.esc_html($value).'</label><br/>';
									}
								?>
							</fieldset>
							<p class="description">Automatic option will display Wakto start time only. Manual option will display Wakto start time + Jama'h time.</p>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table st_auto" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label>Location</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Location</span></legend>
								<?php
									$ltypes = array(
										'0' => 'Select City',
										'1' => 'Custom Location',
									);
									foreach($ltypes as $key=>$value) {
										$checked = $st_options['custom_loc'] == $key ? ' checked' : '';
										echo '<label for="custom_loc'.esc_html($key).'"><input onClick="changeLocation('.esc_html($key).')" id="custom_loc'.esc_html($key).'" type="radio" name="st_options[custom_loc]" value="'.esc_html($key).'"'.$checked.'>'.esc_html($value).'</label><br/>';
									}
								?>
							</fieldset>
							<p class="description">You can select a city from the dropdown list or use Custom Location (if you know your location's latitude, longitude and time zone).</p>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table st_select_city" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label>Select City</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Select City</span></legend>
								<select name="st_options[lat_long_tz]">
									<?php
										$cityArr = [
											// Middle East
											[
												'continent' => 'Middle East',
												'cities' => [
													['name' => 'Abu Dhabi, UAE', 'lat' => '24.467', 'long' => '54.367', 'tz' => '4'],
													['name' => 'Amman, Jordan', 'lat' => '31.94972', 'long' => '35.93278', 'tz' => '2'],
													['name' => 'Baghdad, Iraq', 'lat' => '33.333', 'long' => '44.433', 'tz' => '3'],
													['name' => 'Beirut, Lebanon', 'lat' => '33.9', 'long' => '35.533', 'tz' => '2'],
													['name' => 'Damascus, Syria', 'lat' => '33.51306', 'long' => '36.29194', 'tz' => '2'],
													['name' => 'Doha, Qatar', 'lat' => '25.28667', 'long' => '51.53333', 'tz' => '3'],
													['name' => 'Dubai, UAE', 'lat' => '25.2048', 'long' => '55.2708', 'tz' => '4'],
													['name' => 'Jerusalem, Israel', 'lat' => '31.7683', 'long' => '35.2137', 'tz' => '2'],
													['name' => 'Kuwait City, Kuwait', 'lat' => '29.3759', 'long' => '47.9774', 'tz' => '3'],
													['name' => 'Manama, Bahrain', 'lat' => '26.2285', 'long' => '50.5860', 'tz' => '3'],
													['name' => 'Muscat, Oman', 'lat' => '23.5880', 'long' => '58.3829', 'tz' => '4'],
													['name' => 'Riyadh, Saudi Arabia', 'lat' => '24.7136', 'long' => '46.6753', 'tz' => '3'],
													['name' => 'Jeddah, Saudi Arabia', 'lat' => '21.5433', 'long' => '39.1728', 'tz' => '3'],
													['name' => 'Mecca, Saudi Arabia', 'lat' => '21.4225', 'long' => '39.8262', 'tz' => '3'],
													['name' => 'Medina, Saudi Arabia', 'lat' => '24.5247', 'long' => '39.5692', 'tz' => '3'],
													['name' => 'Sanaa, Yemen', 'lat' => '15.3694', 'long' => '44.1910', 'tz' => '3'],
													['name' => 'Tehran, Iran', 'lat' => '35.6892', 'long' => '51.3890', 'tz' => '3.5'],
													['name' => 'Tel Aviv, Israel', 'lat' => '32.0853', 'long' => '34.7818', 'tz' => '2']
												]
											],
											
											
											// Africa
											[
												'continent' => 'Africa',
												'cities' => [
														['name' => 'Abidjan, Ivory Coast', 'lat' => '5.316667', 'long' => '-4.033333', 'tz' => '0'],
														['name' => 'Addis Ababa, Ethiopia', 'lat' => '9.03', 'long' => '38.74', 'tz' => '3'],
														['name' => 'Algiers, Algeria', 'lat' => '36.766667', 'long' => '3.216667', 'tz' => '1'],
														['name' => 'Antananarivo, Madagascar', 'lat' => '-18.933333', 'long' => '47.516667', 'tz' => '3'],
														['name' => 'Cairo, Egypt', 'lat' => '30.05', 'long' => '31.233', 'tz' => '2'],
														['name' => 'Cape Town, South Africa', 'lat' => '-33.9249', 'long' => '18.4241', 'tz' => '2'],
														['name' => 'Casablanca, Morocco', 'lat' => '33.5731', 'long' => '-7.5898', 'tz' => '0'],
														['name' => 'Dakar, Senegal', 'lat' => '14.692778', 'long' => '-17.446667', 'tz' => '0'],
														['name' => 'Dar es Salaam, Tanzania', 'lat' => '-6.7924', 'long' => '39.2083', 'tz' => '3'],
														['name' => 'Harare, Zimbabwe', 'lat' => '-17.863889', 'long' => '31.029722', 'tz' => '2'],
														['name' => 'Johannesburg, South Africa', 'lat' => '-26.2041', 'long' => '28.0473', 'tz' => '2'],
														['name' => 'Kampala, Uganda', 'lat' => '0.3476', 'long' => '32.5825', 'tz' => '3'],
														['name' => "Khartoum, Sudan", 'lat' => '15.5007', 'long' => '32.5599', 'tz' => '2'],
														['name' => 'Kinshasa, DR Congo', 'lat' => '-4.4419', 'long' => '15.2663', 'tz' => '1'],
														['name' => 'Lagos, Nigeria', 'lat' => '6.5244', 'long' => '3.3792', 'tz' => '1'],
														['name' => 'Lusaka, Zambia', 'lat' => '-15.3875', 'long' => '28.3228', 'tz' => '2'],
														['name' => 'Nairobi, Kenya', 'lat' => '1.2921', 'long' => '36.8219', 'tz' => '3'],
														['name' => 'Rabat, Morocco', 'lat' => '34.0209', 'long' => '-6.8416', 'tz' => '0'],
														['name' => "Tripoli, Libya", "lat" => "32.8872", "long" => "13.1913", "tz" => "2"],
														['name' => "Tunis, Tunisia", "lat" => "36.8065", "long" => "10.1815", "tz" => "1"]
												]
											],
											
											// Europe
											[
												'continent' => 'Europe',
												'cities' => [
													['name' => 'Amsterdam, Netherlands', 'lat' => '52.366667', 'long' => '4.9', 'tz' => '1'],
													['name' => 'Athens, Greece', 'lat' => '37.966667', 'long' => '23.716667', 'tz' => '2'],
													['name' => 'Barcelona, Spain', 'lat' => '41.3851', 'long' => '2.1734', 'tz' => '1'],
													['name' => 'Berlin, Germany', 'lat' => '52.516667', 'long' => '13.383333', 'tz' => '1'],
													['name' => 'Brussels, Belgium', 'lat' => '50.8503', 'long' => '4.3517', 'tz' => '1'],
													['name' => 'Bucharest, Romania', 'lat' => '44.4268', 'long' => '26.1025', 'tz' => '2'],
													['name' => 'Budapest, Hungary', 'lat' => '47.4925', 'long' => '19.051389', 'tz' => '1'],
													['name' => 'Copenhagen, Denmark', 'lat' => '55.6761', 'long' => '12.5683', 'tz' => '1'],
													['name' => 'Dublin, Ireland', 'lat' => '53.347778', 'long' => '-6.259722', 'tz' => '0'],
													['name' => 'Edinburgh, Scotland', 'lat' => '55.9533', 'long' => '-3.1883', 'tz' => '0'],
													['name' => 'Frankfurt, Germany', 'lat' => '50.1109', 'long' => '8.6821', 'tz' => '1'],
													['name' => 'Geneva, Switzerland', 'lat' => '46.2044', 'long' => '6.1432', 'tz' => '1'],
													['name' => 'Helsinki, Finland', 'lat' => '60.170833', 'long' => '24.9375', 'tz' => '2'],
													['name' => 'Istanbul, Turkey', 'lat' => '41.0082', 'long' => '28.9784', 'tz' => '3'],
													['name' => 'Kyiv, Ukraine', 'lat' => '50.4501', 'long' => '30.5234', 'tz' => '2'],
													['name' => 'Lisbon, Portugal', 'lat' => '38.7223', 'long' => '-9.1393', 'tz' => '0'],
													['name' => 'London, UK', 'lat' => '51.5074', 'long' => '-0.1278', 'tz' => '0'],
													['name' => 'Madrid, Spain', 'lat' => '40.4168', 'long' => '-3.7038', 'tz' => '1'],
													['name' => 'Milan, Italy', 'lat' => '45.4642', 'long' => '9.1900', 'tz' => '1'],
													['name' => 'Moscow, Russia', 'lat' => '55.7558', 'long' => '37.6173', 'tz' => '3'],
													['name' => 'Munich, Germany', 'lat' => '48.1351', 'long' => '11.5820', 'tz' => '1'],
													['name' => 'Oslo, Norway', 'lat' => '59.9139', 'long' => '10.7522', 'tz' => '1'],
													['name' => 'Paris, France', 'lat' => '48.8566', 'long' => '2.3522', 'tz' => '1'],
													['name' => 'Prague, Czech Republic', 'lat' => '50.0755', 'long' => '14.4378', 'tz' => '1'],
													['name' => 'Rome, Italy', 'lat' => '41.9028', 'long' => '12.4964', 'tz' => '1'],
													['name' => 'St. Petersburg, Russia', 'lat' => '59.9343', 'long' => '30.3351', 'tz' => '3'],
													['name' => 'Stockholm, Sweden', 'lat' => '59.3293', 'long' => '18.0686', 'tz' => '1'],
													['name' => 'Vienna, Austria', 'lat' => '48.2082', 'long' => '16.3738', 'tz' => '1'],
													['name' => 'Warsaw, Poland', 'lat' => '52.2297', 'long' => '21.0122', 'tz' => '1'],
													['name' => 'Zurich, Switzerland', 'lat' => '47.3769', 'long' => '8.5417', 'tz' => '1']
												]
											],
											
											// Asia
											[
												'continent' => 'Asia',
												'cities' => [
													['name' => 'Almaty, Kazakhstan', 'lat' => '43.2220', 'long' => '76.8512', 'tz' => '6'],
													['name' => 'Astana, Kazakhstan', 'lat' => '51.166667', 'long' => '71.433333', 'tz' => '6'],
													['name' => 'Bangkok, Thailand', 'lat' => '13.75', 'long' => '100.466667', 'tz' => '7'],
													['name' => 'Beijing, China', 'lat' => '39.913889', 'long' => '116.391667', 'tz' => '8'],
													['name' => 'Bengaluru, India', 'lat' => '12.9716', 'long' => '77.5946', 'tz' => '5.5'],
													['name' => 'Chennai, India', 'lat' => '13.0827', 'long' => '80.2707', 'tz' => '5.5'],
													['name' => 'Colombo, Sri Lanka', 'lat' => '6.9271', 'long' => '79.8612', 'tz' => '5.5'],
													['name' => 'Delhi, India', 'lat' => '28.7041', 'long' => '77.1025', 'tz' => '5.5'],
													['name' => 'Dhaka, Bangladesh', 'lat' => '23.7', 'long' => '90.4', 'tz' => '6'],
													['name' => 'Hanoi, Vietnam', 'lat' => '21.033333', 'long' => '105.85', 'tz' => '7'],
													['name' => 'Ho Chi Minh City, Vietnam', 'lat' => '10.8231', 'long' => '106.6297', 'tz' => '7'],
													['name' => 'Hong Kong', 'lat' => '22.3193', 'long' => '114.1694', 'tz' => '8'],
													['name' => 'Hyderabad, India', 'lat' => '17.3850', 'long' => '78.4867', 'tz' => '5.5'],
													['name' => 'Islamabad, Pakistan', 'lat' => '33.7', 'long' => '73.1', 'tz' => '5'],
													['name' => 'Jakarta, Indonesia', 'lat' => '-6.2', 'long' => '106.816667', 'tz' => '7'],
													['name' => 'Kabul, Afghanistan', 'lat' => '34.5553', 'long' => '69.2075', 'tz' => '4.5'],
													['name' => 'Karachi, Pakistan', 'lat' => '24.8607', 'long' => '67.0011', 'tz' => '5'],
													['name' => 'Kathmandu, Nepal', 'lat' => '27.7172', 'long' => '85.3240', 'tz' => '5.75'],
													['name' => 'Kolkata, India', 'lat' => '22.5726', 'long' => '88.3639', 'tz' => '5.5'],
													['name' => 'Kuala Lumpur, Malaysia', 'lat' => '3.1390', 'long' => '101.6869', 'tz' => '8'],
													['name' => 'Lahore, Pakistan', 'lat' => '31.5204', 'long' => '74.3587', 'tz' => '5'],
													['name' => 'Manila, Philippines', 'lat' => '14.5995', 'long' => '120.9842', 'tz' => '8'],
													['name' => 'Mumbai, India', 'lat' => '19.0760', 'long' => '72.8777', 'tz' => '5.5'],
													['name' => 'Osaka, Japan', 'lat' => '34.6937', 'long' => '135.5023', 'tz' => '9'],
													['name' => 'Phnom Penh, Cambodia', 'lat' => '11.5564', 'long' => '104.9282', 'tz' => '7'],
													['name' => 'Pune, India', 'lat' => '18.5204', 'long' => '73.8567', 'tz' => '5.5'],
													['name' => 'Pyongyang, North Korea', 'lat' => '39.0392', 'long' => '125.7625', 'tz' => '9'],
													['name' => 'Seoul, South Korea', 'lat' => '37.5665', 'long' => '126.9780', 'tz' => '9'],
													['name' => 'Shanghai, China', 'lat' => '31.2304', 'long' => '121.4737', 'tz' => '8'],
													['name' => 'Singapore', 'lat' => '1.3521', 'long' => '103.8198', 'tz' => '8'],
													['name' => 'Taipei, Taiwan', 'lat' => '25.0330', 'long' => '121.5654', 'tz' => '8'],
													['name' => 'Tashkent, Uzbekistan', 'lat' => '41.2995', 'long' => '69.2401', 'tz' => '5'],
													['name' => 'Tbilisi, Georgia', 'lat' => '41.7151', 'long' => '44.8271', 'tz' => '4'],
													['name' => 'Tokyo, Japan', 'lat' => '35.6762', 'long' => '139.6503', 'tz' => '9'],
													['name' => 'Ulaanbaatar, Mongolia', 'lat' => '47.8864', 'long' => '106.9057', 'tz' => '8'],
													['name' => 'Vientiane, Laos', 'lat' => '17.9757', 'long' => '102.6331', 'tz' => '7'],
													['name' => 'Yangon, Myanmar', 'lat' => '16.8661', 'long' => '96.1951', 'tz' => '6.5'],
													['name' => 'Yerevan, Armenia', 'lat' => '40.1792', 'long' => '44.4991', 'tz' => '4']
												]
											],
											
											// Americas - North America
											[
												'continent' => 'Americas - North America',
												'cities' => [
													['name' => 'Atlanta, USA', 'lat' => '33.7490', 'long' => '-84.3880', 'tz' => '-5'],
													['name' => 'Boston, USA', 'lat' => '42.3601', 'long' => '-71.0589', 'tz' => '-5'],
													['name' => 'Chicago, USA', 'lat' => '41.8781', 'long' => '-87.6298', 'tz' => '-6'],
													['name' => 'Dallas, USA', 'lat' => '32.7767', 'long' => '-96.7970', 'tz' => '-6'],
													['name' => 'Denver, USA', 'lat' => '39.7392', 'long' => '-104.9903', 'tz' => '-7'],
													['name' => 'Detroit, USA', 'lat' => '42.3314', 'long' => '-83.0458', 'tz' => '-5'],
													['name' => 'Havana, Cuba', 'lat' => '23.1136', 'long' => '-82.3666', 'tz' => '-5'],
													['name' => 'Houston, USA', 'lat' => '29.7604', 'long' => '-95.3698', 'tz' => '-6'],
													['name' => 'Las Vegas, USA', 'lat' => '36.1699', 'long' => '-115.1398', 'tz' => '-8'],
													['name' => 'Los Angeles, USA', 'lat' => '34.0522', 'long' => '-118.2437', 'tz' => '-8'],
													['name' => 'Mexico City, Mexico', 'lat' => '19.4326', 'long' => '-99.1332', 'tz' => '-6'],
													['name' => 'Miami, USA', 'lat' => '25.7617', 'long' => '-80.1918', 'tz' => '-5'],
													['name' => 'Montreal, Canada', 'lat' => '45.5017', 'long' => '-73.5673', 'tz' => '-5'],
													['name' => 'New York, USA', 'lat' => '40.7128', 'long' => '-74.0060', 'tz' => '-5'],
													['name' => 'Orlando, USA', 'lat' => '28.5383', 'long' => '-81.3792', 'tz' => '-5'],
													['name' => 'Ottawa, Canada', 'lat' => '45.4215', 'long' => '-75.6972', 'tz' => '-5'],
													['name' => 'Philadelphia, USA', 'lat' => '39.9526', 'long' => '-75.1652', 'tz' => '-5'],
													['name' => 'Phoenix, USA', 'lat' => '33.4484', 'long' => '-112.0740', 'tz' => '-7'],
													['name' => 'San Francisco, USA', 'lat' => '37.7749', 'long' => '-122.4194', 'tz' => '-8'],
													['name' => 'Seattle, USA', 'lat' => '47.6062', 'long' => '-122.3321', 'tz' => '-8'],
													['name' => 'Toronto, Canada', 'lat' => '43.6532', 'long' => '-79.3832', 'tz' => '-5'],
													['name' => 'Vancouver, Canada', 'lat' => '49.2827', 'long' => '-123.1207', 'tz' => '-8'],
													['name' => 'Washington DC, USA', 'lat' => '38.9072', 'long' => '-77.0369', 'tz' => '-5']
												]
											],
											
											// Americas - Central & South America
											[
												'continent' => 'Americas - Central & South America',
												'cities' => [
													['name' => 'Bogota, Colombia', 'lat' => '4.7110', 'long' => '-74.0721', 'tz' => '-5'],
													['name' => 'Brasilia, Brazil', 'lat' => '-15.793889', 'long' => '-47.882778', 'tz' => '-3'],
													['name' => 'Buenos Aires, Argentina', 'lat' => '-34.603333', 'long' => '-58.381667', 'tz' => '-3'],
													['name' => 'Caracas, Venezuela', 'lat' => '10.5', 'long' => '-66.916667', 'tz' => '-4'],
													['name' => 'Guatemala City, Guatemala', 'lat' => '14.6349', 'long' => '-90.5069', 'tz' => '-6'],
													['name' => 'La Paz, Bolivia', 'lat' => '-16.5000', 'long' => '-68.1500', 'tz' => '-4'],
													['name' => 'Lima, Peru', 'lat' => '-12.0464', 'long' => '-77.0428', 'tz' => '-5'],
													['name' => 'Montevideo, Uruguay', 'lat' => '-34.9011', 'long' => '-56.1645', 'tz' => '-3'],
													['name' => 'Panama City, Panama', 'lat' => '8.9824', 'long' => '-79.5199', 'tz' => '-5'],
													['name' => 'Quito, Ecuador', 'lat' => '-0.1807', 'long' => '-78.4678', 'tz' => '-5'],
													['name' => 'Rio de Janeiro, Brazil', 'lat' => '-22.9068', 'long' => '-43.1729', 'tz' => '-3'],
													['name' => 'San Jose, Costa Rica', 'lat' => '9.9281', 'long' => '-84.0907', 'tz' => '-6'],
													['name' => 'Santiago, Chile', 'lat' => '-33.4489', 'long' => '-70.6693', 'tz' => '-3'],
													['name' => 'Sao Paulo, Brazil', 'lat' => '-23.5505', 'long' => '-46.6333', 'tz' => '-3']
												]
											],
											
											// Oceania
											[
												'continent' => 'Oceania',
												'cities' => [
													['name' => 'Adelaide, Australia', 'lat' => '-34.9285', 'long' => '138.6007', 'tz' => '9.5'],
													['name' => 'Auckland, New Zealand', 'lat' => '-36.8485', 'long' => '174.7633', 'tz' => '12'],
													['name' => 'Brisbane, Australia', 'lat' => '-27.4698', 'long' => '153.0251', 'tz' => '10'],
													['name' => 'Canberra, Australia', 'lat' => '-35.3075', 'long' => '149.124417', 'tz' => '10'],
													['name' => 'Melbourne, Australia', 'lat' => '-37.8136', 'long' => '144.9631', 'tz' => '10'],
													['name' => 'Perth, Australia', 'lat' => '-31.9505', 'long' => '115.8605', 'tz' => '8'],
													['name' => 'Sydney, Australia', 'lat' => '-33.8688', 'long' => '151.2093', 'tz' => '10'],
													['name' => 'Wellington, New Zealand', 'lat' => '-41.2865', 'long' => '174.7762', 'tz' => '12']
												]
											]
										];

										foreach($cityArr as $continent) {
											echo '<optgroup label="'.esc_html($continent['continent']).'">';
											foreach($continent['cities'] as $city) {
												$value = $city['lat'].' '.$city['long'].' '.$city['tz'];
												$selected = $st_options['lat_long_tz'] == $value ? ' selected' : '';
												echo '<option value="'.esc_html($value).'"'.$selected.'>'.esc_html($city['name']).'</option>';
											}
											echo '</optgroup>';
										}
									?>
								</select>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="form-table st_custom_location" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label>Custom Location</label></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Custom Location</span></legend>
								Latitude:<input type="text" maxlength="7" size="5" name="st_options[lat]" value="<?php echo esc_html($st_options['lat']); ?>"/> Longitude:<input type="text" maxlength="7" size="5" name="st_options[long]" value="<?php echo esc_html($st_options['long']); ?>"/> Time Zone:
								<select name="st_options[time_zone]">
									<?php
										$tzArr = json_decode('["-11","-10","-9.30","-9","-8","-7","-6","-5","-4","-3.30","-3","-2","-1","0","1","2","3","3.30","4","4.30","5","5.30","5.45","6","6.30","7","8","8.45","9","9.30","10","10.30","11","12","13","13.45","14"] ', true);

										foreach($tzArr as $value) {
											$label = 'GMT ' . (intval($value) >= 0 ? '+' : '') . $value;
											$selected = $st_options['time_zone'] == $value ? ' selected' : '';
											echo '<option value="'.esc_html($value).'"'.$selected.'>'.esc_html($label).'</option>';
										}
									?>
								</select>
							</fieldset>
							<?php
								//echo json_encode($tzArr);
							?>
						</td>
					</tr>
				</tbody>
			</table>

			<h2 class="title st_auto">Calculation Settings</h2>
			<table class="form-table st_auto" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label>Juristic Method</label> (<a href="#help">?</a>)</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Juristic Method</span></legend>
								<select name="st_options[asr_method]" id="jm">
									<?php
										$asr_methods = array(
											'0' => 'Standard (Shafii, Maliki, Jafari, Hanbali and Salafi)',
											'1' => 'Hanafi'
										);
										foreach ($asr_methods as $key=>$value) {
											$selected = $key == $st_options['asr_method'] ? ' selected' : '';
											echo '<option value="'.esc_html($key).'"'.$selected.'>'.esc_html($value).'</option>';
										}
									?>
								</select>
							</fieldset>
							<p class="description">(For <span style="color: green;">Asr</span> time.)</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>Calculation Method</label> (<a href="#help">?</a>)</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Calculation Method</span></legend>
								<select name="st_options[calc_method]" id="cm">
									<?php
										$calc_methods = array(
											'0' => 'Shia Ithna Ashari (Jafari)',
											'1' => 'University of Islamic Sciences, Karachi',
											'2' => 'Islamic Society of North America (ISNA)',
											'3' => 'Muslim World League (MWL)',
											'4' => 'Umm al-Qura, Makkah',
											'5' => 'Egyptian General Authority of Survey',
											'7' => 'Institute of Geophysics, University of Tehran'
										);
										foreach ($calc_methods as $key=>$value) {
											$selected = $key == $st_options['calc_method'] ? ' selected' : '';
											echo '<option value="'.esc_html($key).'"'.$selected.'>'.esc_html($value).'</option>';
										}
									?>
								</select>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>Higher Latitudes Method</label> (<a href="#help">?</a>)</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><span>Higher Latitudes Method</span></legend>
								<select name="st_options[highlats]" id="highlats">
									<?php
										$highlats = array(
											'0' => 'No Adjustment',
											'1' => 'Middle of Night Method',
											'2' => '1/7th of the Night Method',
											'3' => 'Angle-Based Method (recommended)'
										);
										foreach ($highlats as $key=>$value) {
											$selected = $key == $st_options['highlats'] ? ' selected' : '';
											echo '<option value="'.esc_html($key).'"'.$selected.'>'.esc_html($value).'</option>';
										}
									?>
								</select>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="postbox">
				<h3 class="hndle" style="padding: 10px; margin: 0;"><span>Widget Settings</span></h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<td><label for="wgt_title">Widget Title:</label>
							</td>
							<td><input id="wgt_title" type="text" maxlength="99" name="st_options[wgt_title1]" value="<?php echo esc_html($st_options['wgt_title1']); ?>"/> </td>
						</tr>
						<tr valign="top">
							<td><label for="ln">Location Name:</label>
							</td>
							<td><input id="ln" type="text" maxlength="99" name="st_options[location]" value="<?php echo esc_html($st_options['location']); ?>"/> <span style="color: green;">(Will be displayed on widget.)</span>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="tf">Time Format:</label>
							</td>
							<td>
								<select name="st_options[time_format]" id="tf">
									<option value="0" <?php if($st_options[ 'time_format']=="0" ) { echo " selected"; } ?>>24 Hour</option>
									<option value="1" <?php if($st_options[ 'time_format']=="1" ) { echo " selected"; } ?>>12 Hour</option>
									<option value="2" <?php if($st_options[ 'time_format']=="2" ) { echo " selected"; } ?>>12 Hour (No suffix)</option>
									<option value="3" <?php if($st_options[ 'time_format']=="3" ) { echo " selected"; } ?>>Floating point number</option>
								</select> Use "<span style="color: red;">12 Hour (No suffix)</span>" for "<span style="color: red;">Bengali</span>" language.
							</td>
						</tr>
						<tr valign="top">
							<td><label for="daylight">Daylight Saving:</label>
							</td>
							<td>
								<select name="st_options[daylight]" id="daylight">
									<option value="1" <?php if($st_options[ 'daylight']=="1" ) { echo " selected"; } ?>>On</option>
									<option value="0" <?php if($st_options[ 'daylight']=="0" ) { echo " selected"; } ?>>Off</option>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="sd">Show Date:</label>
							</td>
							<td>
								<p><input id="sd" type="checkbox" id="show_date" name="st_options[show_date]" value="1" <?php if($st_options[ 'show_date']==1) echo( 'checked="checked"'); ?>/><label for="sd">Gregorian Date</label>
								</p>
								<p><input id="shd" type="checkbox" id="show_hdate" name="st_options[show_hdate]" value="1" <?php if(isset($st_options[ 'show_hdate']) && $st_options[ 'show_hdate']==1) echo( 'checked="checked"'); ?>/><label for="shd">Hijri Date</label>
								</p>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="hijri_adjust">Adjust Hijri Date (±):</label>
							</td>
							<td><input id="hijri_adjust" type="text" maxlength="3" size="4" name="st_options[hijri_adjust]" value="<?php echo esc_html($st_options['hijri_adjust']); ?>"/> Hours</td>
						</tr>
						<tr valign="top">
							<td><label for="lang">Language:</label>
							</td>
							<td>
								<select name="st_options[lang]" id="lang">
									<option value="en" <?php if($st_options[ 'lang']=="en" ) { echo " selected"; } ?>>English</option>
									<option value="bn" <?php if($st_options[ 'lang']=="bn" ) { echo " selected"; } ?>>Bengali</option>
									<option value="custom" <?php if($st_options[ 'lang']=="custom" ) { echo " selected"; } ?>>Custom (Set below)</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="cl">Custom Language:</label>
							</td>
							<td>
								<p>Change the text: <span style="color: red;">Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah</span>
								</p>
								<p><input size="60" id="cl" type="text" name="st_options[custom]" value="<?php echo esc_html($st_options['custom']); ?>"/>
								</p>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle" style="padding: 10px; margin: 0;"><span>Widget Style</span></h3>
				<div class="inside">
					<table class="form-table">
						<tr valign="top">
							<td><label for="scheme">Color Scheme:</label>
							</td>
							<td>
								<select name="st_options[scheme]" id="scheme">
									<option value="#313232 #ffffff #181818 #ffffff #313232 #585858 #ffffff" <?php if($st_options[ 'scheme']=="#313232 #ffffff #181818 #ffffff #313232 #585858 #ffffff" ) { echo " selected"; } ?>>Black</option>
									<option value="#4189dd #ffffff #4472C4 #ffffff #B4C6E7 #D9E2F3 #000000" <?php if($st_options[ 'scheme']=="#4189dd #ffffff #4472C4 #ffffff #B4C6E7 #D9E2F3 #000000" ) { echo " selected"; } ?>>Blue</option>
									<option value="#4189dd #ffffff #5b9bd5 #ffffff #bdd6ee #deeaf6 #000000" <?php if($st_options[ 'scheme']=="#4189dd #ffffff #5b9bd5 #ffffff #bdd6ee #deeaf6 #000000" ) { echo " selected"; } ?>>Light Blue</option>
									<option value="#778496 #ffffff #65707f #ffffff #dddcdc #f0f0f0 #000000" <?php if($st_options[ 'scheme']=="#778496 #ffffff #65707f #ffffff #dddcdc #f0f0f0 #000000" ) { echo " selected"; } ?>>Gray</option>
									<option value="#48ae03 #ffffff #70ad47 #ffffff #c5e0b3 #e2efd9 #000000" <?php if($st_options[ 'scheme']=="#48ae03 #ffffff #70ad47 #ffffff #c5e0b3 #e2efd9 #000000" ) { echo " selected"; } ?>>Green</option>
									<option value="#ee6204 #ffffff #ed7d31 #ffffff #f7caac #fbe4d5 #000000" <?php if($st_options[ 'scheme']=="#ee6204 #ffffff #ed7d31 #ffffff #f7caac #fbe4d5 #000000" ) { echo " selected"; } ?>>Orange</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="halign">Text Alignment:</label>
							</td>
							<td><label for="halign">Header: </label>
								<select name="st_options[halign]" id="halign">
									<option value="left" <?php if($st_options[ 'halign']=="left" ) { echo " selected"; } ?>>Left</option>
									<option value="center" <?php if($st_options[ 'halign']=="center" ) { echo " selected"; } ?>>Center</option>
									<option value="right" <?php if($st_options[ 'halign']=="right" ) { echo " selected"; } ?>>Right</option>
								</select>
							</td>
							<td><label for="talign">Title: </label>
								<select name="st_options[talign]" id="talign">
									<option value="left" <?php if($st_options[ 'talign']=="left" ) { echo " selected"; } ?>>Left</option>
									<option value="center" <?php if($st_options[ 'talign']=="center" ) { echo " selected"; } ?>>Center</option>
									<option value="right" <?php if($st_options[ 'talign']=="right" ) { echo " selected"; } ?>>Right</option>
								</select>
							</td>
							<td><label for="walign">Wakto/Time: </label>
								<select name="st_options[walign]" id="walign">
									<option value="left" <?php if($st_options[ 'walign']=="left" ) { echo " selected"; } ?>>Left</option>
									<option value="center" <?php if($st_options[ 'walign']=="center" ) { echo " selected"; } ?>>Center</option>
									<option value="right" <?php if($st_options[ 'walign']=="right" ) { echo " selected"; } ?>>Right</option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="width">Table width:</label>
							</td>
							<td colspan="3"><input id="width" type="text" maxlength="5" name="st_options[width]" value="<?php echo esc_html($st_options['width']); ?>"/> (Example: 90%, 200px etc.)</td>
						</tr>
						<tr valign="top">
							<td><label for="dir">Table/Text direction:</label>
							</td>
							<td colspan="3">
								<select name="st_options[dir]" id="dir">
									<option value="inherit" <?php if($st_options[ 'dir']=="inherit" ) { echo " selected"; } ?>>As is</option>
									<option value="ltr" <?php if($st_options[ 'dir']=="ltr" ) { echo " selected"; } ?>>Left to Right</option>
									<option value="rtl" <?php if($st_options[ 'dir']=="rtl" ) { echo " selected"; } ?>>Right to Left</option>
								</select> (Use <span style="color: red;">Right to Left</span> for Arabic, Hebrew etc.)
							</td>
						</tr>
					</table>
				</div>
			</div>

			<?php submit_button(); ?>
			<input type="hidden" name="form_submitted" value="1">
		</form>

		<form method="post" action="options.php">
			<?php settings_fields( 'salat-times-settings-group' ); ?>

			<input type="hidden" name="restore_defaults" value="1">
			<input type="submit" value="Restore Default Settings" class="button button-secondary">
		</form>

		<br/>

		<div class="postbox st_manual">
			<h3 class="hndle" style="padding: 10px; margin: 0;"><span>Manual Time Data Input Panel</span></h3>
			<div class="inside">
				<?php
				
				$dir = WP_CONTENT_DIR . '/plugin_data/salat-times/';
				$file = $dir . 'manual_time.data';

				if ( !file_exists( $file ) ) {
					mkdir( $dir, 0777, true );
					fopen( $file, 'w' )or die( "ERROR! Can't create data file!" );
				}

				// check if form has been submitted
				if ( isset( $_POST[ 'text' ] ) ) {
					// save the text contents
					$textArr = explode("\n", $_POST['text']);
					$i = -1;
					foreach($textArr as $line) {
						$i++;
						$textArr[$i] = sanitize_text_field($line);
					}
					$text = implode("\n", $textArr);
					file_put_contents( $file, $text );
				}

				// read the textfile
				$text = file_get_contents( $file );

				?>
				<!-- HTML form -->
				<form action="" method="post">
					<textarea placeholder="input text" rows="20" cols="100" name="text" style="width: 100%;"><?php echo esc_html($text) ?></textarea>
					<br/>
					<input type="hidden" value="1" name="time_data_updated"/>
					<input class="button button-primary" type="submit" value="Update Data"/>
					<input class="button button-secondary" type="reset" value="Reset"/>
				</form><br/>
				<p><strong>Instrustions:</strong>
				</p>
				<p>Input like this: <strong>Day--Fajr Begining--Zuhr--Asr-Magrib-Isha--Fajr Jama'h--Zuhr--Asr-Magrib-Isha--Sunrise</strong><br/>Use double hyphen "--" as separetor and one line for one day.</p>
				<p><strong>Example:</strong>
				</p>
				<p>Jan 01--05:13 AM--01:15 PM--03:30 PM--06:10 PM--08:15 PM--05:30 AM--01:30 PM--03:45 PM--06:15 PM--08:30 PM--05:50 AM<br/>Jan 02--05:14 AM--01:15 PM--03:30 PM--06:10 PM--08:15 PM--05:30 AM--01:30 PM--03:45 PM--06:15 PM--08:30 PM--05:50 AM<br/>Jan 03--05:14 AM--01:15 PM--03:30 PM--06:10 PM--08:15 PM--05:30 AM--01:30 PM--03:45 PM--06:15 PM--08:30 PM--05:50 AM</p>

			</div>
		</div>

		<a name="help"></a> <a name="custom_plug"></a>
		<div class="postbox">
			<h3 class="hndle" style="padding: 10px; margin: 0;"><span><a name="help"></a>Help</span></h3>
			<div class="inside">
				<p style="text-align: center; padding-top: 10px; padding-bottom: 10px; color: red; font-size: 18px; font-weight: bold; border: 1px solid red;">Do you need any custom feature? Please send a mail to imran4dev@gmail.com</p>

				<a name="how"></a>
				<p><strong><u>How To Use</u>:</strong>
				</p>
				<p style="padding-left: 10px;">Go to: Appearance > <a href="<?php admin_url(); ?>widgets.php">Widgets</a> to use this (Daily Salat Times) widget.</p>
				<p style="padding-left: 10px;">Insert this shortcode in post/page: <code><span style="color: #000000"><span style="color: #0000BB">[daily_salat_times]</span></span></code>
				</p>
				<p style="padding-left: 10px;">Or, PHP code: <code><span style="color: #000000"><span style="color: #0000BB">   &#60;&#63;php echo do_shortcode&#40;&#39;[daily_salat_times]&#39;&#41;; </span><span style="color: #0000BB">&#63;&#62;</span></span></code>
				</p>
				<p><strong><u>Juristic Methods</u>:</strong>
				</p>
				<p style="padding-left: 10px;" align="justify">There are two main opinions on how to calculate Asr time. The majority of schools (including Shafi'i, Maliki, Ja'fari, and Hanbali) say it is at the time when the length of any object's shadow equals the length of the object itself plus the length of that object's shadow at noon. The dominant opinion in the Hanafi school says that Asr begins when the length of any object's shadow is twice the length of the object plus the length of that object's shadow at noon.</p>
				<p><strong><u>Calculation Methods</u>:</strong>
				</p>
				<p style="padding-left: 10px;" align="justify">There are different conventions for calculating prayer times. The following table lists several well-known conventions currently in use in various regions:</p>
				<table style="border-collapse:collapse;">
					<tr>
						<th style="border: 1px solid silver; background-color: #CCC;">Method</th>
						<th style="border: 1px solid silver; background-color: #CCC;">Region Used</th>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Muslim World League</td>
						<td style="border: 1px solid silver; padding-left: 5px;">Europe, Far East, parts of US</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Islamic Society of North America</td>
						<td style="border: 1px solid silver; padding-left: 5px;">North America (US and Canada)</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Egyptian General Authority of Survey </td>
						<td style="border: 1px solid silver; padding-left: 5px;"> Africa, Syria, Lebanon, Malaysia</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Umm al-Qura University, Makkah </td>
						<td style="border: 1px solid silver; padding-left: 5px;"> Arabian Peninsula</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">University of Islamic Sciences, Karachi</td>
						<td style="border: 1px solid silver; padding-left: 5px;">Pakistan, Afganistan, Bangladesh, India</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Institute of Geophysics, University of Tehran</td>
						<td style="border: 1px solid silver; padding-left: 5px;">Iran, Some Shia communities</td>
					</tr>
					<tr>
						<td style="border: 1px solid silver; padding-left: 5px;">Shia Ithna Ashari, Leva Research Institute, Qum &nbsp;</td>
						<td style="border: 1px solid silver; padding-left: 5px;">Some Shia communities worldwide</td>
					</tr>
				</table>
				<p><strong><u>Higher Latitudes Methods</u>:</strong></p>
				<p>In locations at higher latitude, twilight may persist throughout the night during some months of the year. In these abnormal periods, the determination of Fajr and Isha is not possible using the usual formulas mentioned in the previous section. To overcome this problem, several solutions have been proposed, three of which are described below. </p>
				<ol>
					<li><strong>Middle of the Night:</strong> In this method, the period from sunset to sunrise is divided into two halves. The first half is considered to be the "night" and the other half as "day break". Fajr and Isha in this method are assumed to be at mid-night during the abnormal periods. </li>
					<li><strong>One-Seventh of the Night:</strong> In this method, the period between sunset and sunrise is divided into seven parts. Isha begins after the first one-seventh part, and Fajr is at the beginning of the seventh part.</li>
					<li><strong>Angle-Based Method:</strong> This is an intermediate solution, used by some recent prayer time calculators. Let α be the twilight angle for Isha, and let t = α/60. The period between sunset and sunrise is divided into t parts. Isha begins after the first part. For example, if the twilight angle for Isha is 15, then Isha begins at the end of the first quarter (15/60) of the night. Time for Fajr is calculated similarly.</li>
				</ol>
				<p>In case Maghrib is not equal to Sunset, we can apply the above rules to Maghrib as well to make sure that Maghrib always falls between Sunset and Isha during the abnormal periods. </p>
			</div>
		</div>

		<div class="postbox">
			<h3 class="hndle" style="padding: 10px; margin: 0;"><span>Credits</span></h3>
			<div class="inside">
				<table class="form-table">
					<tr valign="top">
						<td>
							<p><a href="https://facebook.com/imran2w" target="_blank"><img src="https://www.gravatar.com/avatar/<?php echo md5( "imran4dev@gmail.com" ); ?>" /></a>
							</p>
							<p>Developer: <a href="https://facebook.com/imran2w">ALI IMRAN</a><br/> E-Mail: imran4dev@gmail.com<br/> Web: <a target="_blank" href="https://imran.link">imran.link</a>
							</p><br/>
							<p align="justify">This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or ( at your option) any later version. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the <a href="http://www.gnu.org/licenses/gpl.txt">GNU General Public License</a> for more details.</p><br/>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<script>
			function el(id) {
				return document.getElementById(id);
			}

			function changeTimetable(type) {
				document.getElementById('submit').click();
			}

			function changeLocation(type) {
				(function($){
					if(type == 0) {
						$('.st_select_city').show();
						$('.st_custom_location').hide();
					} else {
						$('.st_select_city').hide();
						$('.st_custom_location').show();
					}
				})(jQuery);
			}

			document.addEventListener('DOMContentLoaded', function() { 
				let type = <?= $st_options['timetable'] ?>;
				(function($){
					if(type == 0) {
						$('.st_auto').show();
						$('.st_manual').hide();
						changeLocation(<?= $st_options['custom_loc'] ?>);
					} else {
						$('.st_auto').hide();
						$('.st_manual').show();

						$('.st_select_city').hide();
						$('.st_custom_location').hide();
					}
				})(jQuery);
			}, false);
		</script>

	</div>
	<?php
}

function salat_times_admin() {
	global $salat_times_hook;
	$salat_times_hook = add_options_page( 'Salat Times Settings', 'Salat Times', 'activate_plugins', 'salat_times', 'salat_times_options_page' );
}

function register_salat_times_settings() {
	register_setting( 'salat-times-settings-group', 'st_options' );
	register_setting( 'salat-times-settings-group2', 'tt_options' );
}

add_action( 'admin_menu', 'salat_times_admin' );
add_action( 'admin_init', 'register_salat_times_settings' );

?>