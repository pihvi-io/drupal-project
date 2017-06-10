<?php
/**
 * This file contains Drupal config
 */

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Set up our global environment constant
 * Default: development
 */
defined( 'DRUPAL_ENV' ) or define( 'DRUPAL_ENV', strtolower( env( 'DRUPAL_ENV' ) ) ?: 'development' );

/**
 * Database settings - Use env MYSQL_DATABASE, MYSQL_USER, MYSQL_PWD, MYSQL_HOST from docker first
 * but fallback to docker container links
 */
$databases['default']['default'] = array (
  'database'  => env( 'MYSQL_DATABASE' ) ?: env( 'MYSQL_PASSWORD' ),
  'username'  => env( 'MYSQL_USER' ),
  'password'  => env( 'MYSQL_PWD' ) ?: env( 'MYSQL_PASSWORD' ),
  'host'      => env( 'MYSQL_HOST' ),
  'port'      => env( 'MYSQL_PORT' ),
  'prefix'    => env( 'DB_PREFIX' ) ?: '',
  
  'driver'    => env( 'DB_DRIVER' )       ?: 'mysql',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
);

/**
 * Set salts
 */
$settings['hash_salt'] = env( 'DRUPAL_HASH_SALT' );

if ( (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on")
  || (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https")
  || (isset($_SERVER["HTTP_HTTPS"]) && $_SERVER["HTTP_HTTPS"] == "on")
) {
  $_SERVER["HTTPS"] = "on";

  // Tell Drupal we're using HTTPS (url() for one depends on this).
  $conf['https'] = TRUE;
}

if (isset($_SERVER['REMOTE_ADDR'])) {
  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_addresses'] = array($_SERVER['REMOTE_ADDR']);
}

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

/**
 * Disable the render cache (this includes the page cache).
 *
 * Note: you should test with the render cache enabled, to ensure the correct
 * cacheability metadata is present. However, in the early stages of
 * development, you may want to disable it.
 *
 * This setting disables the render cache by using the Null cache back-end
 * defined by the development.services.yml file above.
 *
 * Do not use this setting until after the site is installed.
 */
# $settings['cache']['bins']['render'] = 'cache.backend.null';

/**
 * Disable Dynamic Page Cache.
 *
 * Note: you should test with Dynamic Page Cache enabled, to ensure the correct
 * cacheability metadata is present (and hence the expected behavior). However,
 * in the early stages of development, you may want to disable it.
 */
# $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

/**
 * Allow test modules and themes to be installed.
 *
 * Drupal ignores test modules and themes by default for performance reasons.
 * During development it can be useful to install test extensions for debugging
 * purposes.
 */
$settings['extension_discovery_scan_tests'] = TRUE;

/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;

/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;

/**
 * Flysystem s3 settings for using AWS S3 / Minio for file storage
 */
$schemes = [
  's3' => [
    'driver' => 's3',
    'config' => [
      'key'    => env('AWS_S3_ACCESS_KEY') ?: env('MINIO_ACCESS_KEY'),   // 'key' and 'secret' do not need to be
      'secret' => env('AWS_S3_SECRET_KEY') ?: env('MINIO_SECRET_KEY'),   // provided if using IAM roles.
      'region' => env('AWS_S3_REGION'),
      'bucket' => env('AWS_S3_BUCKET'),
    ],
    'cache' => TRUE, // Creates a metadata cache to speed up lookups.
  ],
];

// An alternative API endpoint for minio
if ( env('MINIO_HOSTNAME') ) {
  $schemes['s3']['config']['endpoint'] = 'http://' . env('MINIO_HOSTNAME');
}

$settings['flysystem'] = $schemes;

/**
 * Load custom configs according to DRUPAL_ENV environment variable
 */
$env_config = "{__DIR__}/{DRUPAL_ENV}.settings.php";

if ( file_exists( $env_config ) ) {
    include_once $env_config;
}
