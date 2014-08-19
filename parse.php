<?php

error_reporting ( E_ALL );


//$data = json_decode ( file_get_contents ( 'data.json' ), 1 );

if ( ! $data  ) {
  $data = array();
}


$pages = scandir('pages/');

foreach ( $pages as $k => $v )
  {
    list ( $slug ) = explode ( '.', $v, 2 );

    if ( in_array ( substr($v, 0, 1), array ( '.', '#' ), true ) ) continue;
    $page = file_get_contents ( 'pages/'.$v );

    if ( ! $page ) continue;

    $data[$slug] = array();
    
    $m = null;
    
    preg_match ( '/<title>(.*)- ASX Listed Company Information Fact Sheet<\\/title>/', $page, $m );

    if ( isset ( $m[1] ) )
      {
        $data[$slug]['title'] = $m[1];
      }

    preg_match ( '/<th>Registered Office Address<\\/th>[^<]*<td>([^<]*)<\\/td>/', $page, $m );

    if ( isset ( $m[1] ) )
      {
        $data[$slug]['address'] = $m[1];
      }

    preg_match ( '/<th>Head Office Telephone<\\/th>[^<]*<td>([^<]*)<\\/td>/', $page, $m );

    if ( isset ( $m[1] ) )
      {
        $data[$slug]['phone'] = $m[1];
      }
    
  }

$s = "";

foreach ( $data as $k => $v )
  {
    $s .=  $k . "\t" . str_replace ( "\t", " ", $v['title'] ) . "\t" . str_replace ( "\t", " ", $v['address'] ) . "\t" . str_replace ( "\t", " ", $v['phone'] ) . "\n";
  }

file_put_contents ( 'data.csv', $s );
//file_put_contents ( 'data.json', json_encode ( $data, 1 ) );

?>