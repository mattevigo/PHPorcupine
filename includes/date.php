<?php
/**
 * EXPERIMENTAL
 */
define( "DATE_PHPORCUPINE", "Y/m/d H:i" );

/**
 * Esegue il parsing della data dividendola nell'array 
 * 
 * @param $date
 * @return unknown_type
 */
function datepicker_parse($date)
{
	$splitted = array();
	
	$splitted['month'] = substr($date, 3, 2);
	$splitted['day'] = substr($date, 0, 2);
	$splitted['year'] = substr($date, 6, 4);
	
	return $splitted;
}

/**
 * Esegue il parsing della data dividendola nell'array 
 * 
 * @param $date
 * @return unknown_type
 */
function datepicker_reverse($date)
{
	$splitted = array();
	
	$splitted['month'] = substr($date, 4, 2);
	$splitted['day'] = substr($date, 6, 2);
	$splitted['year'] = substr($date, 0, 4);
	
	return $splitted['day']."/".$splitted['month']."/".$splitted['year'];
}

/**
 * Trasforma una data in timestamp
 * 
 * @param $date
 * @param $hr
 * @param $min
 * @param $sec
 * @return unknown_type
 */
function datepicker_timestamp($date, $hr=0, $min=0, $sec=0)
{
	$cal = datepicker_parse($date);
	return mktime((int) $hr, (int) $min, (int) $sec, (int) $cal['month'], (int) $cal['day'], (int) $cal['year']);
}

function timestamp2datearray( $time )
{
	$date_str = date(DATE_PHPORCUPINE, $time);
	
	$date_arr = array();
	
	$date_arr['cal'] = substr($date_str, 0, 10);
	$date_arr['hr'] = substr($date_str, 11, 2);
	$date_arr['min'] = substr($date_str, 14, 2);
	
	//echo $date_str . "<br />";
	
	return $date_arr;
}