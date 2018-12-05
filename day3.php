<?php
include 'input.txt';
$input = file( 'input.txt' );

$prepared_arr = array();

foreach( $input as $row ) {

    $sub_str = explode( ' ', $row );
    $idx = $sub_str[0];
    $offsets = explode( ',', str_replace(':', '', $sub_str[2]) );
    $size = explode( 'x', $sub_str[3] );

    array_push( $prepared_arr, array(
        'id' => $idx,
        'offset_x' => $offsets[0],
        'offset_y' => $offsets[1],
        'size_x' => $size[0],
        'size_y' => $size[1],
    ));    
}


function shuffle_assoc($list) {
    if (!is_array($list)) return $list;
  
    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
      $random[$key] = $list[$key];
  
    return $random;
} 

while ( sizeof($prepared_arr) !== 1 ) {

    // echo "IN WHILE size of: " . sizeof($prepared_arr) . ".\n";

    $remove_these = array();
    $fabric = array();

    // echo "Prepared arr index 0 BEFORE shuffle " . $prepared_arr[0]['id'] . ".\n";
    shuffle( $prepared_arr );
    // echo "Prepared arr index 0 AFTER shuffle " . $prepared_arr[0]['id'] . ".\n";


    foreach ( $prepared_arr as $item ) {
        $overlap = false;
        for ( $width = 0; $width < $item['size_x']; $width++ ) {
            for ( $height = 0; $height < $item['size_y']; $height++ ) {

                $x_distance = $item['offset_x'] + $width;
                $y_distance = $item['offset_y'] + $height;
    
                // Nothing exists in this square, empty cell
                if ( !isset( $fabric [ $x_distance ][ $y_distance ] ) ) {
                    echo "Found empty cell, printing " . $item['id'] . " to it.\n";
                    $fabric[ $x_distance ][ $y_distance ] = $item['id'];
                // There is something else in that cell, intersection detected.
                } else {
                    // echo "Overlap at " . $x_distance . "," . $y_distance . ".\n";
                    $overlap = true;
                    $fabric[ $x_distance ][ $y_distance ] = 'X';
                }
            }
        }
        if ( $overlap == true ) {
            // Items to be remove from main array
            array_push( $remove_these, $item );
        }
    }

    // echo "Remove these length " . sizeof( $remove_these ) . ".\n";

    foreach ( $remove_these as $r_item ) {
        foreach ( $prepared_arr as $key => $item ) {
            if ( $r_item['id'] == $item['id'] ) {
                echo "Unsetting " . $item['id'] . ".\n";
                unset( $prepared_arr[$key] );
            }
        }
    }
    
}

echo "Prepared array length: " . sizeof($prepared_arr) . ".\n";
echo "Index of first element " . $prepared_arr[0]['id'] . ".\n";



echo "\n";