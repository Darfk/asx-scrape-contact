<?php

error_reporting ( E_ALL );

function scrape($url, &$info) {
  $proxy = 'socks5://localhost:9050';
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

$codes = file_get_contents ( 'codes.csv' );
$codes = explode ("\n", $codes);
$codes = array_map ( 'trim', $codes );

foreach ( $codes as $k => $code )
  {
    if ( ! $code )
      {
        unset ( $codes[$k] );
      }
  }

@mkdir('pages', 0700, true);

$u = 'http://www.asx.com.au/asx/research/companyInfo.do?by=asxCode&asxCode=';

foreach ( $codes as $k => $code )
  {
    $f = 'pages/' . $code . '.html';
    if ( ! file_exists ( $f ) )
      {

        $info = null;
        $res = scrape ( $u . $code, $info );

        if ( $info['http_code'] !== 200 )
          {
            print_r ( $info );
            break;
          }

        file_put_contents ( $f, $res );
        printf("%s\n", $code);
      }
  }

?>