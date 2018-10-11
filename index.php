<?php

/**
 * PHP version 5.4.
 *
 * @author     Anindya Sundar Mandal <anindya@SpeedUpWebsite.info>
 * @copyright  2018 Anindya Sundar Mandal
 * @license    https://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause "New" or "Revised" License
 *
 * @version    1.0.0
 *
 * @see       https://SpeedUpWebsite.info
 * @since      Class available since Release 1.0.0
 */

    //Display all error
    error_reporting(E_ALL);

    //Set display_errors ON if it is OFF by default
    if (!ini_get('display_errors')) {
        ini_set('display_errors', '1');
    }

    if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
        die("You need at least PHP 5.4.0\n");
    }

    if (!extension_loaded('openssl')) {
        die("You need OpenSSL extension enabled with PHP\n");
    }

    if (!extension_loaded('curl')) {
        die("You need Curl extension enabled with PHP\n");
    }

    if (!extension_loaded('mysqli')) {
        die("You need Mysqli extension enabled with PHP\n");
    }
    
    if (!ini_get('allow_url_fopen')) {
        die("You need to set PHP directive allow_url_fopen = On. Please contact your web hosting company for help.");
    }

    // Define Directory Separator to make the default DIRECTORY_SEPARATOR short
    define('DS', DIRECTORY_SEPARATOR);

    $config_file_path = __DIR__.DS.'config'.DS.'config.php';

    // Check if wp-config.php has been created
    if (!file_exists($config_file_path)) {
        header('location: install.php');
    }

    // Initialize the session
    if (!session_id()) {
        session_start();
    }

    // If session variable 'email' is not set redirect to login page
    if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
        header('location: login.php');
        exit;
    }

    //Include config file
    require_once $config_file_path;

    // Composer autoloading
    include __DIR__.DS.'vendor'.DS.'autoload.php';

    require_once __DIR__.DS.'vendor'.DS.'usrflo'.DS.'registered-domain-libs'.DS.'PHP'.DS.'effectiveTLDs.inc.php';
    require_once __DIR__.DS.'vendor'.DS.'usrflo'.DS.'registered-domain-libs'.DS.'PHP'.DS.'regDomain.inc.php';

    use FreeSslDotTech\FreeSSLAuto\Admin\Admin;
    use FreeSslDotTech\FreeSSLAuto\Admin\Layout;

    //add header, sidebar, message
    $layout = new Layout();
    $layout->header();
    $layout->sidebar();
    $layout->message();

    $action = isset($_GET['action']) ? $_GET['action'] : 'index';
    $action = filter_var($action, FILTER_SANITIZE_STRING);

    global $mysqli;

    $admin = new Admin($config_file_path, $mysqli);

        //add the page as per request
    switch ($action) {
        case 'index':
            $admin->index();

            break;
        case 'settings-basic':
            $admin->basicSettings();

            break;
        case 'settings-cpanel':
            $admin->cpanelSettings();

            break;
        case 'settings-cpanel-domains-to-exclude':
            $admin->cpanelExcludeDomainsSettings();

            break;
        case 'settings-domains':
            $admin->domainsSettings();

            break;
        case 'settings-add-domain':
            $admin->addDomainSettings();

            break;
        case 'settings-delete-domain':
            $admin->deleteDomainSettings();

            break;
        case 'settings-dns-service-providers':
            $admin->dnsServiceProvidersSettings();

            break;
        case 'settings-add-dns-service-provider':
            $admin->addDnsServiceProvidersSettings();

            break;
        case 'settings-delete-dns-provider':
            $admin->deleteDnsProvidersSettings();

            break;
        case 'settings-add-cron':
            $admin->addCronJobSettings();

            break;
        case 'revoke-ssl':
            $admin->revokeSsl();

            break;
        case 'issue-free-ssl':
            include 'cron.php';

            break;
        case 'change-le-account-key':
            $admin->keyChange();

            break;
        case 'change-password':
            $admin->changePassword();

            break;
        case 'update-profile':
            $admin->updateProfile();

            break;
        default:
            $admin->pageNotFound();
    }

        //add the footer
        $layout->footer();
