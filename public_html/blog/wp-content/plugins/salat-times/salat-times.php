<?php
/*
Plugin Name: Salat Times
Plugin URI: http://i-onlinemedia.net/
Description: Salat (Namaz) timetable for any location around the world, based on a variety of calculation methods currently used in muslim communities.
Author: M.A. IMRAN
Version: 3.2
Author URI: http://facebook.com/imran2w
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


function daily_salat_times() {

	$st_options = get_option( "st_options" );
	if ( !is_array( $st_options ) ) {
		$st_options = array(
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
			'timetable' => '0' );
	}


	if ( !function_exists( 'en_to_bn' ) ) {
		function en_to_bn( $str ) {
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
				'lm3' => 'Rabi-Al-Awaal',
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
				'lm3' => 'রবিউল-আউয়াল',
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
	}


	if ( !$st_options[ 'timetable' ] == '1' ) { // Automatic Timetable
		
		$highLatsMethod = '0';
		if(isset($st_options[ 'highlats' ])) {
			$highLatsMethod = $st_options['highlats'];
		}

		$prayTime = new PrayTime();
		$prayTime->setCalcMethod( $st_options[ 'calc_method' ] );
		$prayTime->setAsrMethod( $st_options[ 'asr_method' ] );
		$prayTime->setTimeFormat( $st_options[ 'time_format' ] );
		$prayTime->setHighLatsMethod($highLatsMethod);

		$location = explode( " ", $st_options[ 'lat_long_tz' ] );

		if ( $st_options[ 'custom_loc' ] == '0' ) {
			$latitude = $location[ 0 ];
			$longitude = $location[ 1 ];
			$time_zone = $location[ 2 ] + $st_options[ 'daylight' ];
		} else {
			$latitude = $st_options[ 'lat' ];
			$longitude = $st_options[ 'long' ];
			$time_zone = $st_options[ 'time_zone' ] + $st_options[ 'daylight' ];
		}

		$d = new uCal;
		$offset = $time_zone * 60 * 60;
		$hoffset = $st_options[ 'hijri_adjust' ] * 60 * 60;

		if ( $st_options[ 'lang' ] == 'bn' ) {
			if ( $st_options[ 'show_date' ] == '1' ) {
				$stdate = '<br/>' . en_to_bn( gmdate( "l, jS F, Y", time() + $offset ) );
			}
			if ( $st_options[ 'show_hdate' ] == '1' ) {
				$sthdate = '<br/>' . en_to_bn( $d->date( "jS F, Y", time() + $hoffset ) );
			}
		} else {
			if ( $st_options[ 'show_date' ] == '1' ) {
				$stdate = '<br/>' . gmdate( "l, jS F, Y", time() + $offset );
			}
			if ( $st_options[ 'show_hdate' ] == '1' ) {
				$sthdate = '<br/>' . $d->date( "jS F, Y", time() + $hoffset );
			}
		}

		$color = explode( " ", $st_options[ 'scheme' ] );

		if ( $st_options[ 'lang' ] == "en" ) {
			$cl = explode( "-", "Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah" );
		} else {
			$cl = explode( "-", $st_options[ 'custom' ] );
		}

		if ( $st_options[ 'lang' ] == "bn" ) {
			$times = en_to_bn( $prayTime->getPrayerTimes( time(), $latitude, $longitude, $time_zone ) );
			print( '<table style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . '; border-collapse: collapse;">
	<tr><td colspan="2" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . '; border: 1px solid white;">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
	<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">ওয়াক্ত</td><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">সময়</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">সুবহে সাদিক</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">ভোর ' . $times[ 0 ] . '</td></tr>' . '
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">সূর্যোদয়</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">ভোর ' . $times[ 1 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">যোহর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">দুপুর ' . $times[ 2 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">আছর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">বিকাল ' . $times[ 3 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">মাগরিব</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">সন্ধ্যা ' . $times[ 5 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">এশা</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;"> রাত ' . $times[ 6 ] . '</td></tr>
	</table>' );
		} else {
			$times = $prayTime->getPrayerTimes( time(), $latitude, $longitude, $time_zone );
			print( '<table style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . '; border-collapse: collapse;">
	<tr><td colspan="2" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . '; border: 1px solid white;">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
	<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">' . $cl[ 0 ] . '</td><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">' . $cl[ 1 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 0 ] . '</td></tr>' . '
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 3 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 1 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 2 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 3 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 6 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 5 ] . '</td></tr>
	<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 7 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 6 ] . '</td></tr>
	</table>' );
		}
	} else { // Manual Timetable

		$dir = WP_CONTENT_DIR . '/plugin_data/salat-times/';
		$file = $dir . 'manual_time.data';
		$lines = file( $file ); //file in to an array

		$lineN = gmdate( "z", time() + $offset );

		$color = explode( " ", $st_options[ 'scheme' ] );

		if ( $st_options[ 'lang' ] == "en" ) {
			$cl = explode( "-", "Salat-Time-Fajr-Sunrise-Zuhr-Asr-Magrib-Isha-Begins-Jamah" );
		} else {
			$cl = explode( "-", $st_options[ 'custom' ] );
		}

		if ( $st_options[ 'lang' ] == "bn" ) {
			$times = en_to_bn( explode( "--", $lines[ $lineN ] ) );
			print( '<table style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . '; border-collapse: collapse;">
		<tr><td colspan="3" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . '; border: 1px solid white;">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
		<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td rowspan="2" style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">ওয়াক্ত</td><td colspan="2" style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">সময়</td></tr>
		<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">ওয়াক্ত শুরু</td><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">জাম’আত</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">ফযর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">ভোর ' . $times[ 1 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">ভোর ' . $times[ 6 ] . '</td></tr>' . '
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">সূর্যোদয়</td><td colspan="2" style="text-align: center; border: 1px solid white; padding-left: 3px;">ভোর ' . $times[ 11 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">যোহর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">দুপুর ' . $times[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;"> দুপুর ' . $times[ 7 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">আছর</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">বিকাল ' . $times[ 3 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">বিকাল ' . $times[ 8 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">মাগরিব</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">সন্ধ্যা ' . $times[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">সন্ধ্যা ' . $times[ 9 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">এশা</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">রাত ' . $times[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 3px;">রাত ' . $times[ 10 ] . '</td></tr>
		</table>' );
		} else {
			$times = explode( "--", $lines[ $lineN ] );
			print( '<table style="direction: ' . $st_options[ 'dir' ] . '; width: ' . $st_options[ 'width' ] . '; border-collapse: collapse;">
		<tr><td colspan="3" style="text-align: ' . $st_options[ 'halign' ] . '; background-color: ' . $color[ 0 ] . '; color: ' . $color[ 1 ] . '; border: 1px solid white;">' . $st_options[ 'location' ] . $stdate . $sthdate . '</td></tr>
		<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td rowspan="2" style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">' . $cl[ 0 ] . '</td><td colspan="2" style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">' . $cl[ 1 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 2 ] . '; color: ' . $color[ 3 ] . ';"><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">Begins</td><td style="text-align: ' . $st_options[ 'talign' ] . '; border: 1px solid white;">Jamah</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 1 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 6 ] . '</td></tr>' . '
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 3 ] . '</td><td colspan="2" style="text-align: center; border: 1px solid white; padding-left: 10px;">' . $times[ 11 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 2 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 7 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 3 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 8 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 4 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 6 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 4 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 9 ] . '</td></tr>
		<tr style="background-color: ' . $color[ 5 ] . '; color: ' . $color[ 6 ] . ';"><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $cl[ 7 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 5 ] . '</td><td style="text-align: ' . $st_options[ 'walign' ] . '; border: 1px solid white; padding-left: 10px;">' . $times[ 10 ] . '</td></tr>
		</table>' );
		}
	}
}


function widget_daily_salat( $args ) {
	extract( $args );
	$st_options = get_option( "st_options" );
	if ( !is_array( $st_options ) ) {
		$st_options = array( 'wgt_title1' => 'Salat Times' );
	}
	echo $before_widget;
	echo $before_title . $st_options[ 'wgt_title1' ] . $after_title;
	?>
	<ul>
		<?php echo daily_salat_times(); ?>
	</ul>
	<?php
	echo $after_widget;
}


function widget_daily_salat_control() {
	$bddp_options = get_option( "st_options" );
	if ( !is_array( $st_options ) ) {
		$st_options = array(
			'wgt_title1' => 'Salat Times',
			'location' => 'Dhaka, Bangladesh' );
	}
	?>

	<p>
		<table width="100%">
			<tr>
				<td>Widget Title:</td>
				<td>
					<span style="color: green;">
						<?php echo $st_options['wgt_title1']; ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>Location Name:</td>
				<td>
					<span style="color: green;">
						<?php echo $st_options['location']; ?>
					</span>
				</td>
			</tr>
		</table>
	</p>
	<p><span style="color: gray;">Go to: Settings > <a href="<?php admin_url(); ?>options-general.php?page=salat_times">Salat Times</a> to change options.</span>
	</p>
	<?php
}

// ========== Action Links =================

function st_action_links( $links ) {
	$links[] = '<a href="' . get_admin_url( null, 'options-general.php?page=salat_times' ) . '">Settings</a>';
	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'st_action_links' );

// =====================

add_shortcode( 'daily_salat_times', 'daily_salat_times' );
wp_register_sidebar_widget( 'daily_salat_times', 'Daily Salat Times', 'widget_daily_salat', array( 'description' => __( 'Displays daily salat/namaz timetable for given location.' ) ) );
wp_register_widget_control( 'daily_salat_times', 'Daily Salat Times', 'widget_daily_salat_control' );

if ( is_admin() )
	include 'salat_times_admin.php';
?>