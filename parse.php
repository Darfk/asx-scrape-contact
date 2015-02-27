<?php

error_reporting ( E_ALL );

$types = array ( 'people' => array ( 'base' => 'http://data.asx.com.au/data/1/company/%s/people',
                                   'dir' => 'scraped/people'),
                 'entity' => array ( 'base' => 'http://data.asx.com.au/data/1/company/%s',
                                     'dir' => 'scraped/entity'));


$pages = array();

foreach ( $types as $key => $type )
  {
    $pages[$key] = scandir($type['dir']);



  }

$data = array();

foreach ( $types as $key => $type )
  {
    foreach ( $pages[$key] as $page )
      {
        if ( in_array ( substr($page, 0, 1), array ( '.', '#' ), true ) ) continue;

        $f = $type['dir'] . '/' . $page;

        if ( ! isset ( $data[$page] ) ) $data[$page] = array();

        $data[$page][$key] = json_decode ( file_get_contents ( $f ), 1 );
        if ( json_last_error() !== JSON_ERROR_NONE ) unset ( $data[$page][$key] );
      }
  }

$rows = array();

foreach ( $data as $entity )
  {
    foreach ( array ( 'directors', 'secretaries' ) as $role )
      {
        if ( isset ( $entity['people'][$role] ) &&
             is_array ( $entity['people'][$role] ) &&
             sizeof ( $entity['people'][$role] ) > 0 )
          {
            foreach ( $entity['people'][$role] as $person )
              {
                $rows []= array ( $entity['entity']['code'],
                                  $entity['entity']['name_full'],
                                  $entity['entity']['web_address'],
                                  $entity['entity']['phone_number'],
                                  $entity['entity']['mailing_address'],
                                  $entity['entity']['phone_number'],
                                  $person['salutation'],
                                  $person['first_name'],
                                  $person['middle_name'],
                                  $person['last_name'],
                                  implode ( ',', $person['roles'] )
                                  );
              }
          }
      }
  }

function excel ( $a ) {
  $s = '';
  foreach ( $a as $i )
    {
      $s .= implode ( "\t", str_replace ( array ( "\t", "\n" ), array ( "" ), $i ) ) . "\n";
    }
  return $s;
}

file_put_contents ( 'out.csv', excel ( $rows ) );

?>