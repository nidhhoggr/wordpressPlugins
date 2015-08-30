<?php

require_once('../../../../../wp-load.php');
require_once('../IngestCsv.php');
require_once('../CsvLib.php');

$ic = new \SupraCsvPremium\IngestCsv();

$settings = array(
    'scsv_has_hooks' => true,
    'scsv_post' => array (
        'publish' => '1',
        'type' => 'post',
        'title' => 'default title',
        'desc' => 'default description',
    ),
    'scsv_parse_terms' => true,
    'scsv_custom_terms' => '',
    'scsv_postmeta' => array(
        'meta_key' => array(
            0 => '_edit_lock',
            1 => '_gc_price',
            2 => '_gc_savings',
            3 => '_su_desc',
            4 => '_su_keywords',
            5 => '_su_title',
            6 => '_encloseme',
            7 => '_geo_city',
        ),
        'displayname' => array(
            0 => '_edit_lock',
            1 => '_gc_price',
            2 => '_gc_savings',
            3 => '_su_desc',
            4 => '_su_keywords',
            5 => '_su_title',
            6 => '_encloseme',
            7 => '_geo_city',
        ),
        'use_metakey' => array(
            0 => '1',
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '7',
        ),
    ),
    'scsv_misc_options' => array(
        'is_ingestion_chunked' => '1',
        'chunk_by_n_rows' => '50',
    ),
    'scsv_ingest_debugger' => true,
    'scsv_report_issue' => true,
    'scsv_user' => array(
        'name' => 'admin',
        'pass' => 'admin',
    ),
    'scsv_encode_special_chars' => true,
    'scsv_csv_settings' => array(
        'delimiter' => ',',
        'enclosure' => '\\"',
        'escape' => '\\\\',
    ),
    'scsv_additional_csv_settings' => array(
        'line_maxlen' => 0,
    )
);

$settings = array(
  'scsv_has_hooks' => '',
  'scsv_post' => array(
    'publish' => '1',
    'type' => 'post',
    'title' => '',
    'desc' => '',
  ),
  'scsv_parse_terms' => '',
  'scsv_custom_terms' => 'continents,country,state,lga,district,postal-codes',
  'scsv_postmeta' => array(
    'use_metakey' => array(
      0 => '0',
      1 => '1',
      2 => '2',
      3 => '3',
      4 => '4',
      5 => '5',
      6 => '6',
      7 => '7',
      8 => '8',
      9 => '9',
      10 => '10',
      11 => '11',
      12 => '12',
      13 => '13',
      14 => '14',
      15 => '15',
      16 => '16',
      17 => '17',
      18 => '18',
      19 => '19',
      20 => '20',
      21 => '21',
    ),
    'meta_key' => array(
      0 => 'iso',
      1 => 'language',
      2 => 'geo_id',
      3 => 'region1',
      4 => 'region2',
      5 => 'region3',
      6 => 'region4',
      7 => 'corrected_locality',
      8 => 'locality',
      9 => 'postcode',
      10 => 'suburb',
      11 => 'latitude',
      12 => 'longitude',
      13 => 'elevation',
      14 => 'iso2',
      15 => 'fips',
      16 => 'nuts',
      17 => 'hasc',
      18 => 'stat',
      19 => 'timezone',
      20 => 'utc',
      21 => 'dst',
      22 => 'com_disabled_sh',
      23 => '_edit_last',
      24 => '_edit_lock',
      25 => '_wp_trash_meta_status',
      26 => '_wp_trash_meta_time',
      27 => 'continent',
      28 => 'country',
      29 => 'district',
      30 => 'lga',
      31 => 'postal_code',
      32 => 'post_date',
      33 => 'post_excerpt',
      34 => 'post_type',
      35 => 'state',
      36 => '_corrected_locality',
      37 => '_dst',
      38 => '_elevation',
      39 => '_fips',
      40 => '_geo_id',
      41 => '_hasc',
      42 => '_iso',
      43 => '_iso2',
      44 => '_language',
      45 => '_latitude',
      46 => '_locality',
      47 => '_longitude',
      48 => '_nuts',
      49 => '_postcode',
      50 => '_region1',
      51 => '_region2',
      52 => '_region3',
      53 => '_region4',
      54 => '_stat',
      55 => '_suburb',
      56 => '_timezone',
      57 => '_utc',
    ),
    'displayname' => array(
      0 => 'iso',
      1 => 'language',
      2 => 'geo_id',
      3 => 'region1',
      4 => 'region2',
      5 => 'region3',
      6 => 'region4',
      7 => 'corrected_locality',
      8 => 'locality',
      9 => 'postcode',
      10 => 'suburb',
      11 => 'latitude',
      12 => 'longitude',
      13 => 'elevation',
      14 => 'iso2',
      15 => 'fips',
      16 => 'nuts',
      17 => 'hasc',
      18 => 'stat',
      19 => 'timezone',
      20 => 'utc',
      21 => 'dst',
      22 => 'com_disabled_sh',
      23 => '_edit_last',
      24 => '_edit_lock',
      25 => '_wp_trash_meta_status',
      26 => '_wp_trash_meta_time',
      27 => 'continent',
      28 => 'country',
      29 => 'district',
      30 => 'lga',
      31 => 'postal_code',
      32 => 'post_date',
      33 => 'post_excerpt',
      34 => 'post_type',
      35 => 'state',
      36 => '_corrected_locality',
      37 => '_dst',
      38 => '_elevation',
      39 => '_fips',
      40 => '_geo_id',
      41 => '_hasc',
      42 => '_iso',
      43 => '_iso2',
      44 => '_language',
      45 => '_latitude',
      46 => '_locality',
      47 => '_longitude',
      48 => '_nuts',
      49 => '_postcode',
      50 => '_region1',
      51 => '_region2',
      52 => '_region3',
      53 => '_region4',
      54 => '_stat',
      55 => '_suburb',
      56 => '_timezone',
      57 => '_utc',
    ),
  ),
  'scsv_misc_options' => array(
    'is_ingestion_chunked' => '1',
    'chunk_by_n_rows' => '100',
    'are_revisions_skipped' => '1',
    'is_using_multithreads' => '1',
  ),
  'scsv_ingest_debugger' => '',
  'scsv_report_issue' => '',
  'scsv_user' => array(
    'name' => 'admin',
    'pass' => 'admin',
  ),
  'scsv_encode_special_chars' => 'true',
  'scsv_csv_settings' => array(
    'delimiter' => ',',
    'enclosure' => '\"',
    'escape' => '\\',
  ),
  'scsv_additional_csv_settings' => array(
    'line_maxlen' => '1000000',
  )
); 

$scp = new \SupraCsvPremium\SupraCsvParser(null, $settings);

$settingsResolver = (function($setting_key) use($scp) {

    $settings = $scp->getSettings();

    if(in_array($setting_key, array_keys($settings)))
    {
        $setting = $settings[$setting_key];
    }
    else
    {
        $scp->getLogger()->info('tried to retrieve a non-existing setting: ' . $setting_key);
    }

    return $setting;
});

$scp->setSettingsResolver($settingsResolver);

$scp->init($settings);

$ic->setSupraCsvParser($scp);

$mapping = array(
    'post_title' => 'post_title',
    'post_content' => 'post_content',
    'category' => 'category',
    'post_type' => '',
    'post_status' => 'post_status',
    'post_excerpt' => 'post_excerpt',
    'comment_status' => 'comment_status',
    'terms_continents' => 'continent',
    'terms_country' => 'country',
    'terms_state' => 'state',
    'terms_lga' => 'lga',
    'terms_district' => 'district',
    'terms_postal-codes' => 'postal_code',
    'iso' => 'iso',
    'language' => 'language',
    'geo_id' => 'geo_id',
    'region1' => 'region1',
    'region2' => 'region2',
    'region3' => 'region3',
    'region4' => 'region4',
    'corrected_locality' => 'corrected_locality',
    'locality' => 'locality',
    'postcode' => 'postcode',
    'suburb' => 'suburb',
    'latitude' => 'latitude',
    'longitude' => 'longitude',
    'elevation' => 'elevation',
    'iso2' => 'iso2',
    'fips' => 'fips',
    'nuts' => 'nuts',
    'hasc' => 'hasc',
    'stat' => 'stat',
    'timezone' => 'timezone',
    'utc' => 'utc',
    'dst' => 'dst'
);

$ic->ingest([
    'filename'=>'ondo.csv',
    'mapping'=> $mapping
]);
