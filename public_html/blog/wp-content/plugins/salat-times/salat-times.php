<?php
/*
Plugin Name: Salat Times
Plugin URI: https://imran.link/
Description: Salat (Namaz) timetable for any location around the world, based on a variety of calculation methods currently used in muslim communities.
Author: M.A. IMRAN
Version: 3.7
Author URI: https://facebook.com/imran2w
*/

/*
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or ( at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA.
Online: http://www.gnu.org/licenses/gpl.txt
*/

// Bismillah...

defined( 'ABSPATH' )or die( 'Stop! You can not do this!' );

include_once( 'PrayTime.php' );

if ( !class_exists( 'uCal' ) ) {
	include_once( 'uCal.php' );
}

function st_en2bn( $str ) {
	$enMonth = array(
		'lm1' => 'January',
		'lm2' => 'February',
		'lm3' => 'March',
		'lm4' => 'April',
		'lm5' => 'May',
		'lm6' => 'June',
		'lm7' => 'July',
		'lm8' => 'August',
		'lm9' => 'September',
		'lm10' => 'October',
		'lm11' => 'November',
		'lm12' => 'December',

		'hm1' => 'Muharram',
		'hm2' => 'Safar',
		'hm3' => 'Rabi-Al-Awaal',
		'hm4' => 'Rabi-Al-Thani',
		'hm5' => 'Jumada-Al-Awaal',
		'hm6' => 'Jumada-Al-Thani',
		'hm7' => 'Rajab',
		'hm8' => 'Shaaban',
		'hm9' => 'Ramadan',
		'hm10' => 'Shawwal',
		'hm11' => 'Dhu al-Qidah',
		'hm12' => 'Dhu al-Hijjah'
	);
	$enWeeks = array(
		'ld1' => 'Saturday',
		'ld2' => 'Sunday',
		'ld3' => 'Monday',
		'ld4' => 'Tuesday',
		'ld5' => 'Wednesday',
		'ld6' => 'Thursday',
		'ld7' => 'Friday'
	);
	$bnMonth = array(
		'lm1' => 'জানুয়ারি',
		'lm2' => 'ফেব্রুয়ারি',
		'lm3' => 'মার্চ',
		'lm4' => 'এপ্রিল',
		'lm5' => 'মে',
		'lm6' => 'জুন',
		'lm7' => 'জুলাই',
		'lm8' => 'আগস্ট',
		'lm9' => 'সেপ্টেম্বর',
		'lm10' => 'অক্টোবর',
		'lm11' => 'নভেম্বর',
		'lm12' => 'ডিসেম্বর',

		'hm1' => 'মুহাররম',
		'hm2' => 'সফর',
		'hm3' => 'রবিউল-আউয়াল',
		'hm4' => 'রবিউস-সানি',
		'hm5' => 'জমাদিউল-আউয়াল',
		'hm6' => 'জমাদিউস-সানি',
		'hm7' => 'রজব',
		'hm8' => 'শাবান',
		'hm9' => 'রামাযান',
		'hm10' => 'শাওয়াল',
		'hm11' => 'জিলক্বদ',
		'hm12' => 'জিলহজ্জ'
	);
	$bnWeeks = array(
		'ld1' => 'শনিবার',
		'ld2' => 'রবিবার',
		'ld3' => 'সোমবার',
		'ld4' => 'মঙ্গলবার',
		'ld5' => 'বুধবার',
		'ld6' => 'বৃহস্পতিবার',
		'ld7' => 'শুক্রবার'
	);

	$mergeA1 = array_merge( $enMonth, $enWeeks );
	$mergeA2 = array_merge( $bnMonth, $bnWeeks );

	array_push( $mergeA1, 'AM', 'PM', 'st', 'th', 'nd', 'rd', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	array_push( $mergeA2, '', '', '', '', '', '', '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯' );

	return str_ireplace( $mergeA1, $mergeA2, $str );
}


function daily_salat_times() {

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

	$d = new uCal;
	$hoffset = esc_html($st_options[ 'hijri_adjust' ]) * 60 * 60;

	$stdate = '';
	$sthdate = '';

	if ( $st_options[ 'lang' ] == 'bn' ) {
		if ( $st_options[ 'show_date' ] == '1' ) {
			$stdate = '<br/>' . st_en2bn( wp_date( "l, jS F, Y") );
		}
		if ( $st_options[ 'show_hdate' ] == '1' ) {
			$sthdate = '<br/>' . st_en2bn( $d->date( "jS F, Y", current_time('timestamp') + $hoffset ) );
		}
	} else {
		if ( $st_options[ 'show_date' ] == '1' ) {
			$stdate = '<br/>'.wp_date( "l, jS F, Y");
		}
		if ( $st_options[ 'show_hdate' ] == '1' ) {
			$sthdate = '<br/>' . $d->date( "jS F, Y", current_time('timestamp') + $hoffset );
		}
	}

	ob_start(); // begin output buffering

	if ( !$st_options[ 'timetable' ] == '1' ) { // Automatic Timetable
		
		$latitude = 23.7;
		$longitude = 90.4;
		$time_zone = 6;
		
		$highLatsMethod = '0';
		if(isset($st_options[ 'highlats' ])) {
			$highLatsMethod = esc_html($st_options['highlats']);
		}

		$prayTime = new PrayTime();
		$prayTime->setCalcMethod( $st_options[ 'calc_method' ] );
		$prayTime->setAsrMethod( $st_options[ 'asr_method' ] );
		$prayTime->setTimeFormat( $st_options[ 'time_format' ] );
		$prayTime->setHighLatsMethod($highLatsMethod);

		$location = explode( " ", esc_html($st_options[ 'lat_long_tz' ]) );

		if ( $st_options[ 'custom_loc' ] == '0' ) {
			$latitude = $location[ 0 ];
			$longitude = $location[ 1 ];
			$time_zone = $location[ 2 ] + esc_html($st_options[ 'daylight' ]);
		} else {
			$latitude = $st_options[ 'lat' ];
			$longitude = $st_options[ 'long' ];
			$time_zone = $st_options[ 'time_zone' ] + esc_html($st_options[ 'daylight' ]);
		}

		$color = explode( " ", $st_options[ 'scheme' ] );

		if ( $st_options[ 'lang' ] == "en" ) {
			$cl = explode( "-", "Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah" );
		} else {
			$cl = explode( "-", esc_html($st_options[ 'custom' ]) );
		}

		if ( $st_options[ 'lang' ] == "bn" ) {
			$times = st_en2bn( $prayTime->getPrayerTimes( time(), $latitude, $longitude, $time_zone ) );
			print( '<table class="st_table" style="font-family: \'Noto Serif Bengali\'; direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . ';">
			<tr><td colspan="2" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . ';">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
			<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><th style="text-align: ' . $st_options[ 'talign' ] . ';">ওয়াক্ত</th><th style="text-align: ' . $st_options[ 'talign' ] . ';">সময়</th></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">সুবহে সাদিক</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">ভোর ' . $times[ 0 ] . '</td></tr>' . '
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">সূর্যোদয়</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">ভোর ' . $times[ 1 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">যোহর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">দুপুর ' . $times[ 2 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">আছর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">বিকাল ' . $times[ 3 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">মাগরিব</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">সন্ধ্যা ' . $times[ 5 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">এশা</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;"> রাত ' . $times[ 6 ] . '</td></tr>
			</table>' );
		} else {
			$times = $prayTime->getPrayerTimes( time(), $latitude, $longitude, $time_zone );
			print( '<table class="st_table" style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . ';">
			<tr><td colspan="2" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . ';">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
			<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><th style="text-align: ' . $st_options[ 'talign' ] . ';">' . $cl[ 0 ] . '</th><th style="text-align: ' . $st_options[ 'talign' ] . ';">' . $cl[ 1 ] . '</th></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 0 ] . '</td></tr>' . '
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 3 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 1 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 2 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 3 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 6 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 5 ] . '</td></tr>
			<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $cl[ 7 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . $times[ 6 ] . '</td></tr>
			</table>' );
		}
	} else { // Manual Timetable
		
		$time_zone = $st_options[ 'time_zone' ];
		$offset = $time_zone * 60 * 60;

		$dir = WP_CONTENT_DIR . '/plugin_data/salat-times/';
		$file = $dir . 'manual_time.data';
		$lines = file( $file ); //file in to an array

		$lineN = gmdate( "z", time() + $offset );

		$color = explode( " ", $st_options[ 'scheme' ] );

		if ( $st_options[ 'lang' ] == "en" ) {
			$cl = explode( "-", "Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah" );
		} else {
			$cl = explode( "-", esc_html($st_options['custom']) );
		}

		if(isset($lines[ $lineN ])) {
			if ( $st_options[ 'lang' ] == "bn" ) {
				$times = explode( "--", st_en2bn( str_ireplace( ['AM', 'PM'], ['', ''], $lines[ $lineN ] ) ) );
				print( '<table class="st_table" style="font-family: \'Noto Serif Bengali\'; direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . ';">
				<tr><td colspan="3" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . ';">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>

				<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td style="text-align: ' . $st_options[ 'talign' ] . ';">ওয়াক্ত</td><td style="text-align: ' . $st_options[ 'talign' ] . ';">শুরু</td><td style="text-align: ' . $st_options[ 'talign' ] . ';">জামা’আত</td></tr>

				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">ফযর</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 1 ]) . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 6 ]) . '</td></tr>' . '
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">সূর্যোদয়</td><td colspan="2" style="text-align: center;">' . trim($times[ 11 ]) . '</td></tr>
				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">যোহর</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 2 ]) . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 7 ]) . '</td></tr>
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">আছর</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 3 ]) . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 8 ]) . '</td></tr>
				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">মাগরিব</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 4 ]) . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 9 ]) . '</td></tr>
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . ';">এশা</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 5 ]) . '</td><td style="text-align: ' . $st_options[ 'walign' ] . ';">' . trim($times[ 10 ]) . '</td></tr>
				</table>' );
			} else {
				$times = explode( "--", $lines[ $lineN ] );
				print( '<table class="st_table" style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . ';">
				<tr><td colspan="3" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . ';">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>

				<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><th style="text-align: ' . $st_options[ 'talign' ] . ';">' . $cl[ 0 ] . '</th><th style="text-align: ' . $st_options[ 'talign' ] . ';">' . $cl[ 8 ] . '</th><th style="text-align: ' . $st_options[ 'talign' ] . ';">' . $cl[ 9 ] . '</th></tr>

				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 1 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 6 ] . '</td></tr>' . '
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 3 ] . '</td><td colspan="2" style="text-align: center; padding-left: 10px;">' . $times[ 11 ] . '</td></tr>
				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 7 ] . '</td></tr>
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 3 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 8 ] . '</td></tr>
				<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 6 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 9 ] . '</td></tr>
				<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $cl[ 7 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; padding-left: 10px;">' . $times[ 10 ] . '</td></tr>
				</table>' );
			}
		} else {
			echo '<p style="color: red;">No timetable found for today! Please fill in the full year time for this date in the manual timetable file.</p>';
		}
	}

	$output = ob_get_contents(); // end output buffering
	ob_end_clean(); // grab the buffer contents and empty the buffer
	return $output;
}


function widget_daily_salat( $args ) {
	extract( $args );
	$st_options = get_option( "st_options" );
	if ( !is_array( $st_options ) ) {
		$st_options = array( 'wgt_title1' => 'Salat Times' );
	}
	echo $before_widget;
	echo $before_title . esc_html($st_options[ 'wgt_title1' ]) . $after_title;
	?>
	<ul>
		<?php echo daily_salat_times(); ?>
	</ul>
	<?php
	echo $after_widget;
}


function widget_daily_salat_control() {
	$st_options = get_option( "st_options" );
	if ( !is_array( $st_options ) ) {
		$st_options = array(
			'wgt_title1' => 'Salat Times',
			'location' => 'Dhaka, Bangladesh'
		);
	}
	?>

	<p>
		<table width="100%">
			<tr>
				<td>Widget Title:</td>
				<td>
					<span style="color: green;">
						<?php echo esc_html($st_options['wgt_title1']); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>Location Name:</td>
				<td>
					<span style="color: green;">
						<?php echo esc_html($st_options['location']); ?>
					</span>
				</td>
			</tr>
		</table>
	</p>
	<p><span style="color: gray;">Go to: Settings > <a href="<?php admin_url(); ?>options-general.php?page=salat_times">Salat Times</a> to change options.</span></p>
	<?php
}

// ========== Action Links =================

function st_action_links( $links ) {
	$links[] = '<a href="' . get_admin_url( null, 'options-general.php?page=salat_times' ) . '">Settings</a>';
	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'st_action_links' );

// ========== Enqueue Styles =================

function st_enqueue_styles() {
	wp_enqueue_style( 'salat-times-styles', plugins_url( 'styles.css', __FILE__ ), array(), '3.6' );
}

add_action( 'wp_enqueue_scripts', 'st_enqueue_styles' );
add_action( 'admin_enqueue_scripts', 'st_enqueue_styles' );

// =====================

add_shortcode( 'daily_salat_times', 'daily_salat_times' );
wp_register_sidebar_widget( 'daily_salat_times', 'Daily Salat Times', 'widget_daily_salat', array( 'description' => __( 'Displays daily salat/namaz timetable for given location.' ) ) );
wp_register_widget_control( 'daily_salat_times', 'Daily Salat Times', 'widget_daily_salat_control' );

if ( is_admin() )
	include 'salat_times_admin.php';
?>