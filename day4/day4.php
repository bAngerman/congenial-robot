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

usort( $clean_arr, 'date_compare' );

// echo json_encode( $clean_arr );

// We have a sorted array, lets do shit with it...
$guard_arr = array();
