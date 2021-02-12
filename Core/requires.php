<?php
//ini_set( 'session.cookie_secure', 1 );
ini_set('session.cookie_httponly', 1);
date_default_timezone_set('Europe/London');
//header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
//header('Content-Security-Policy:script-src \'self\' https://code.jquery.com https://*.gamblersden.com https://*.google.com https://*.google-analytics.com https://www.googletagmanager.com https://storage.googleapis.com https://*.twitter.com https://*.twimg.com https://www.gstatic.com https://www.youtube.com https://s.ytimg.com https://cdn.jsdelivr.net http://cdn.datatables.net https://cdnjs.cloudflare.com \'unsafe-inline\';');

header('X-XSS-Protection: 1');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-Powered-By: TheKing');
header('X-Clacks-Overhead: GNU Terry Pratchett');

$GLOBALS['cwd'] = getcwd().'/';

/* BEGIN AUTOLOAD - The @ Surpresses errors.*/
spl_autoload_register(function ($class) {

    $possiblePlaces = [
        'Abstracts/',
        'Core/Functions/',
        'Core/Components/',
        'Core/Components/Views/',
        'Core/ErrorReporting/',
        'Core/DB/'
    ];
    $possibleNameSpacedPlaces = ['', 'Core/ThirdParty/'];

    foreach ($possiblePlaces as $possiblePlace) {
        $fileName = $GLOBALS['cwd'].$possiblePlace . $class . '.php';
        if (file_exists($fileName)) {
            @include_once $fileName;
            return;
        }
    }


    $SwitchSlashes = \StringFunctions::switchToForwardSlashes($class);

    foreach ($possibleNameSpacedPlaces as $possibleNameSpacedPlace) {
        $fileName = $GLOBALS['cwd'].$possibleNameSpacedPlace . $SwitchSlashes . '.php';
        $cwd = getcwd();
        if (file_exists($fileName)) {
            @include_once $fileName;
            return;
        }
    }
});

set_error_handler("ErrorHandler::HandlePHPError");
register_shutdown_function('ErrorHandler::HandlePHPFatal');

require_once "vendor/autoload.php";
/* BEGIN REGULAR REQUIRES */
require_once 'config.php';

\Model\Utilities\TimeZone::SetTZ();
if(php_sapi_name() == "litespeed" || php_sapi_name() == "apache2" || php_sapi_name() == "apache2handler"){
    \Model\Utilities\DomainUtilities::forceHTTPS();
} else {
    \SiteSession::$skipCheck = true;
}

defined('SESSIONNAME') ? \SiteSession::checkgenSession() : false;


