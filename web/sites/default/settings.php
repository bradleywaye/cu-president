<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

// Override pantheon temporary directory
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
  $settings["file_temp_path"] = "sites/default/files/private/tmp";
}

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * Force HTTPS and set primary domain - Added by jason@A5
 */

if (isset($_ENV['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {
  // Redirect to https://$primary_domain in the Live environment
  if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
    // Replace www.example.com with your registered domain name.
    $primary_domain = 'president.cu.edu';
  }
  else {
    // Redirect to HTTPS on every Pantheon environment.
    $primary_domain = $_SERVER['HTTP_HOST'];
  }

  $requires_redirect = FALSE;

  // Ensure the site is being served from the primary domain.
  if ($_SERVER['HTTP_HOST'] != $primary_domain) {
    $requires_redirect = TRUE;
  }

  // If you're not using HSTS in the pantheon.yml file, uncomment this next block.
  // if (!isset($_SERVER['HTTP_USER_AGENT_HTTPS'])
  //     || $_SERVER['HTTP_USER_AGENT_HTTPS'] != 'ON') {
  //   $requires_redirect = TRUE;
  // }

  if ($requires_redirect === TRUE) {

    // Name transaction "redirect" in New Relic for improved reporting (optional).
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }
  // Drupal 8 Trusted Host Settings
  if (is_array($settings)) {
    $settings['trusted_host_patterns'] = array('^'. preg_quote($primary_domain) .'$');
  }
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (is_readable($ddev_settings) && getenv('IS_DDEV_PROJECT') == 'true') {
  require $ddev_settings;
}

$settings['config_sync_directory'] = '../config/default';
$config['config_split.config_split.local']['status'] = TRUE;

if (defined('PANTHEON_ENVIRONMENT')) {
  $config['config_split.config_split.pantheon']['status'] = TRUE;
  $config['config_split.config_split.local']['status'] = FALSE;
  if ($_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
    $config['config_split.config_split.live']['status'] = TRUE;
  }
}
