<?php

    // start session, and count pages visited
    session_start();
    $page_count = $_SESSION['page_count'] ?? 0;
    $_SESSION['page_count'] = ++$page_count;
    $donate = $_SESSION['show_donate'] ?? true;
    $non_donate_page = false;

    // set application root
    define ("APP_ROOT", dirname(__FILE__, 2));

    // require functions and settings
    require APP_ROOT . '/src/functions.php';
    require APP_ROOT . '/config/config.php';
    require APP_ROOT . '/vendor/autoload.php';

    // autoload classes
    spl_autoload_register(function($class) {
        $path = APP_ROOT . '/src/classes/';
        require $path . $class . '.php';
    });

    // set production error handlers
    if (DEV !== true) {
        set_error_handler('handle_error');
        register_shutdown_function('handle_shutdown');
    }

    // create content management object
    $cms = new CMS($dsn, $username, $password);
    unset($dsn, $username, $password);

    // log user data
    date_default_timezone_set('America/Los_Angeles');
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $referer = $_SERVER['HTTP_REFERER'] ?? ' ';
    $page = $_SERVER['REQUEST_URI'];
    $location = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
    $country = $location->geoplugin_countryName;
    $state = $location->geoplugin_region;
    $city = $location->geoplugin_city;

    $pattern = "/bot|crawl/i";
    $bot = preg_match($pattern, $user_agent);

    $pattern = getenv("IPADDR");
    $developer = preg_match($pattern, $ip);

    if (!$developer and !$bot) {
       $cms->getLogger()->insert($date, $time, $ip, $user_agent, $referer, $page, $country, $state, $city);
    }
?>