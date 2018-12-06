<?php
include 'input.txt';
$input = file( 'input.txt' );
$clean_arr = array();

foreach ( $input as $key => $item ) {
    $tmp_arr = array();
    array_push( $clean_arr, explode( ']', $item ) );
    $clean_arr[$key]['datetime'] = str_replace( '[' , '', $clean_arr[$key][0] );
    $clean_arr[$key]['value'] = trim( $clean_arr[$key][1] );

    unset($clean_arr[$key][0]);
    unset($clean_arr[$key][1]);
}
echo "------------------------------------------------------\n\n";

// echo "Clean array date: " . $clean_arr[0][0] . "\n";
// echo "Clean array val: " . $clean_arr[0][1] . "\n";

function date_compare($a, $b)
{
    $t1 = strtotime($a['datetime']);
    $t2 = strtotime($b['datetime']);
    return $t1 - $t2;
}    

function get_minutes ( $time ) {
    $minutes = 0;

    // Ignoring year...

    list( $y_m_d, $h_m ) = explode( ' ', $time );

    $y_m_d = explode( '-', $y_m_d );
    $h_m = explode( ':', $h_m );

    $minutes += $y_m_d[1] * 1440; // Minutes in day..
    $minutes += $h_m[1]; // Simply add minutes..
    $minutes += $h_m[0] * 60; // Hours * 60 gives minutes

    return $minutes;
}

function get_sleep_duration ( $start , $end )
{
    $start = get_minutes( $start );
    $end = get_minutes( $end );

    return $end - $start;
}


usort( $clean_arr, 'date_compare' );

// echo json_encode( $clean_arr[1] ) . "\n";

// We have a sorted array, lets do shit with it...
$guard_arr = array();

$guard_sleeping = false;
$current_guard = null;
$sleep_start = 0;
$sleep_end = 0;
$sleep_time = 0;

foreach( $clean_arr as $key => $item ) {


    // Switch in shift
    if ( strpos( $item['value'], 'Guard' ) !== false ) {

        $current_guard = (int) preg_replace('/\D/', '', $item['value'] );
        echo "Current guard: " . $current_guard . ".\n";
    }
    // Falls asleep
    else if ( strpos( $item['value'], "falls" ) !== false ) {

        $guard_sleeping = true;
        $sleep_start = $item['datetime'];
    }


    // Wakes up
    else if ( strpos( $item['value'], "wakes up" ) !== false ) {
        $guard_sleeping . "\n";
        $sleep_end = $item['datetime'];

        $sleep_time = get_sleep_duration( $sleep_start, $sleep_end );

        // echo "Start: " . $sleep_start . "\n";
        // echo "End: " . $sleep_end . "\n";
        // echo "Sleep Time: " . $sleep_time . "\n";

        $sleep_data = array(
            'start'     => $sleep_start,
            'end'       => $sleep_end,
            'duration'  => $sleep_time,
            'ID'        => $current_guard,
        );
        $guard_arr[ $current_guard ][ $key ] = $sleep_data;
    }
}


foreach ( $guard_arr as $key => $guard ) {
    $total_sleep_time = 0;
    foreach ( $guard as $val ) {
        $total_sleep_time += $val['duration'];
        // echo json_encode($val) . "\n";
    }
    $guard_arr[$key]['total_sleep_time'] = $total_sleep_time;
}

function max_sleep_time($a, $b)
{
    $t1 = $a['total_sleep_time'];
    $t2 = $b['total_sleep_time'];
    return $t1 - $t2;
} 

usort( $guard_arr, 'max_sleep_time');

$bad_boy = array_pop($guard_arr);

$minute_scores = array();

function get_minute( $start, $duration, $remaining ) {

    list( $y_m_d, $h_m ) = explode( ' ', $start );

    $y_m_d = explode( '-', $y_m_d );
    $h_m = explode( ':', $h_m );

    return $h_m[1] + ( $duration - $remaining );
}

foreach ( $bad_boy as $nap ) {
    echo json_encode($nap) . "\n";

    if ( is_array($nap) ) {
        $duration = $nap['duration'];

        while ( $duration > 0 ) {
            $min = get_minute( $nap['start'], $nap['duration'], $duration );
            
            if ( isset( $minute_scores[$min] ) ) $minute_scores[$min] = $minute_scores[$min] + 1;
            else $minute_scores[$min] = 1;



            $duration--;
        }
    }
}

function sort_minutes($a, $b)
{
    $t1 = $a;
    $t2 = $b;
    return $t1 - $t2;
} 

// usort( $minute_scores, 'sort_minutes');

echo json_encode($minute_scores) . "\n";



