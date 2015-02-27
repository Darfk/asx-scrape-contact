<?php

error_reporting ( E_ALL );

function scrape($url, &$info) {
  $proxy = 'socks5://localhost:9150';
  $ch = curl_init ( $url );
  curl_setopt($ch, CURLOPT_PROXY, $proxy);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $res = curl_exec($ch);
  $info = curl_getinfo ( $ch );
  curl_close($ch);
  return $res;
}

$codes = file_get_contents ( 'codes.tsv' );
$codes = explode ("\n", $codes);
$codes = array_map ( 'trim', $codes );

foreach ( $codes as $k => $code )
  {
    if ( ! $code )
      {
        unset ( $codes[$k] );
      }
  }

$types = array ( 'people' => array ( 'base' => 'http://data.asx.com.au/data/1/company/%s/people',
                                   'dir' => 'scraped/people'),
                 'entity' => array ( 'base' => 'http://data.asx.com.au/data/1/company/%s',
                                     'dir' => 'scraped/entity'));


foreach ( $types as $key => $type )
  {
    @mkdir($type['dir'], 0700, true);
  }

foreach ( $codes as $code )
  {
    foreach ( $types as $key => $type )
      {
        $f = $type['dir'] . '/' . $code;

        if ( file_exists ( $f ) )
          continue;

        $info = null;
        $url = sprintf ( $type['base'], $code );

        $res = scrape ( $url, $info );

        if ( $info['http_code'] === 200 )
          {
            file_put_contents ( $f, $res );
            printf("%s %s\n", $code, $key);
          }
        else
          {
            fwrite ( STDERR, sprintf("%s %s %s\n", $code, $key, $info['http_code'] ) );
          }
      }
  }

?>