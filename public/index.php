<?php

use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Session\Adapter\Files as SessionAdapter;

error_reporting(E_ERROR);//0 t0 disable
ini_set('display_errors', 1);//0 to disable

try {
    /**/
    /**
     * Include services
     */
    require __DIR__ . '/../config/services.php';

    /**  Include **/
    require __DIR__ . '/../apps/library/My.php';
    $repoMy = new My();

    /**
     * The URL component is used to generate all kind of urls in the application
     */
    $di['url'] = function () {
        $url = new UrlResolver();
        $url->setBaseUri('/');
        #$url->setStaticBaseUri('https://d2wv6ukqm6hlac.cloudfront.net/');
        return $url;
    };

    /**
     * Start the session the first time some component request the session service
     */
//    $di['session'] = function () {
//        $session = new SessionAdapter();
//        ini_set('session.gc_maxlifetime', 10000);
//        session_set_cookie_params(10000);
//        $session->start();
//        return $session;
//    };
    /**
     * Handle the request
     */
    $application = new Application();

    /**
     * Assign the DI
     */
    $application->setDI($di);

    /**
     * Include modules
     */
    require __DIR__ . '/../config/modules.php';

    //if ($_GET['_url'] == '/print-content-pdf') {
    echo $application->handle()->getContent();
    //} else {
      // echo preg_replace('!\s+!', ' ', $application->handle()->getContent());
    //}

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
    // Sent Error - Add 27-11
    $message = get_class($e) . ": " . $e->getMessage() . "<br>";
    $message .= " File = " . $e->getFile() . "<br>";
    $message .= " Line = ". $e->getLine() . "<br>";
    $message .= " Code = ". $e->getCode() . "<br>";
    $message .= $e->getTraceAsString(). "<br>";
    $message .= "=================================== USER AGENT ======================================================<br>";
    $message .= $_SERVER['HTTP_USER_AGENT'];
    // All Info User
    $indicesServer = array('PHP_SELF',
        'argv',
        'argc',
        'GATEWAY_INTERFACE',
        'SERVER_ADDR',
        'SERVER_NAME',
        'SERVER_SOFTWARE',
        'SERVER_PROTOCOL',
        'REQUEST_METHOD',
        'REQUEST_TIME',
        'REQUEST_TIME_FLOAT',
        'QUERY_STRING',
        'DOCUMENT_ROOT',
        'HTTP_ACCEPT',
        'HTTP_ACCEPT_CHARSET',
        'HTTP_ACCEPT_ENCODING',
        'HTTP_ACCEPT_LANGUAGE',
        'HTTP_CONNECTION',
        'HTTP_HOST',
        'HTTP_REFERER',
        'HTTP_USER_AGENT',
        'HTTPS',
        'REMOTE_ADDR',
        'REMOTE_HOST',
        'REMOTE_PORT',
        'REMOTE_USER',
        'REDIRECT_REMOTE_USER',
        'SCRIPT_FILENAME',
        'SERVER_ADMIN',
        'SERVER_PORT',
        'SERVER_SIGNATURE',
        'PATH_TRANSLATED',
        'SCRIPT_NAME',
        'REQUEST_URI',
        'PHP_AUTH_DIGEST',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'AUTH_TYPE',
        'PATH_INFO',
        'ORIG_PATH_INFO') ;

    echo '<table cellpadding="10">' ;
    foreach ($indicesServer as $arg) {
        if (isset($_SERVER[$arg])) {
            $message .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
        }
        else {
            $message .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
        }
    }
    echo '</table>' ;

    $result = $repoMy->sendError($message);
    die();
} catch (PDOException $e) {
    echo $e->getMessage();
    // Sent Error - Add 27-11
    $message = get_class($e) . ": ". $e->getMessage() . "<br>";
    $message .= " File=" . $e->getFile() . "<br>";
    $message .= " Line=" . $e->getLine() . "<br>";
    $message .= " Code = ". $e->getCode() . "<br>";
    $message .= $e->getTraceAsString(). "<br>";
    $message .= "======================================== USER AGENT ==================================================<br>";
    $message .= $_SERVER['HTTP_USER_AGENT'];
    // All Info Browser
    $indicesServer = array('PHP_SELF',
        'argv',
        'argc',
        'GATEWAY_INTERFACE',
        'SERVER_ADDR',
        'SERVER_NAME',
        'SERVER_SOFTWARE',
        'SERVER_PROTOCOL',
        'REQUEST_METHOD',
        'REQUEST_TIME',
        'REQUEST_TIME_FLOAT',
        'QUERY_STRING',
        'DOCUMENT_ROOT',
        'HTTP_ACCEPT',
        'HTTP_ACCEPT_CHARSET',
        'HTTP_ACCEPT_ENCODING',
        'HTTP_ACCEPT_LANGUAGE',
        'HTTP_CONNECTION',
        'HTTP_HOST',
        'HTTP_REFERER',
        'HTTP_USER_AGENT',
        'HTTPS',
        'REMOTE_ADDR',
        'REMOTE_HOST',
        'REMOTE_PORT',
        'REMOTE_USER',
        'REDIRECT_REMOTE_USER',
        'SCRIPT_FILENAME',
        'SERVER_ADMIN',
        'SERVER_PORT',
        'SERVER_SIGNATURE',
        'PATH_TRANSLATED',
        'SCRIPT_NAME',
        'REQUEST_URI',
        'PHP_AUTH_DIGEST',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'AUTH_TYPE',
        'PATH_INFO',
        'ORIG_PATH_INFO') ;

    echo '<table cellpadding="10">' ;
    foreach ($indicesServer as $arg) {
        if (isset($_SERVER[$arg])) {
            $message .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
        }
        else {
            $message .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
        }
    }
    echo '</table>' ;

    $result = $repoMy->sendError($message);
    die();
} catch (PhException $e){
    echo $e->getMessage();
    // Sent Error - Add 27-11
    $message = get_class($e) . ": ". $e->getMessage() . "<br>";
    $message .= " File=" . $e->getFile() . "<br>";
    $message .= " Line=" . $e->getLine() . "<br>";
    $message .= " Code = ". $e->getCode() . "<br>";
    $message .= $e->getTraceAsString(). "<br>";
    $message .= "======================================== USER AGENT ==================================================<br>";
    $message .= $_SERVER['HTTP_USER_AGENT'];
    // All Info Browser
    $indicesServer = array('PHP_SELF',
        'argv',
        'argc',
        'GATEWAY_INTERFACE',
        'SERVER_ADDR',
        'SERVER_NAME',
        'SERVER_SOFTWARE',
        'SERVER_PROTOCOL',
        'REQUEST_METHOD',
        'REQUEST_TIME',
        'REQUEST_TIME_FLOAT',
        'QUERY_STRING',
        'DOCUMENT_ROOT',
        'HTTP_ACCEPT',
        'HTTP_ACCEPT_CHARSET',
        'HTTP_ACCEPT_ENCODING',
        'HTTP_ACCEPT_LANGUAGE',
        'HTTP_CONNECTION',
        'HTTP_HOST',
        'HTTP_REFERER',
        'HTTP_USER_AGENT',
        'HTTPS',
        'REMOTE_ADDR',
        'REMOTE_HOST',
        'REMOTE_PORT',
        'REMOTE_USER',
        'REDIRECT_REMOTE_USER',
        'SCRIPT_FILENAME',
        'SERVER_ADMIN',
        'SERVER_PORT',
        'SERVER_SIGNATURE',
        'PATH_TRANSLATED',
        'SCRIPT_NAME',
        'REQUEST_URI',
        'PHP_AUTH_DIGEST',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'AUTH_TYPE',
        'PATH_INFO',
        'ORIG_PATH_INFO') ;

    echo '<table cellpadding="10">' ;
    foreach ($indicesServer as $arg) {
        if (isset($_SERVER[$arg])) {
            $message .= '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
        }
        else {
            $message .= '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
        }
    }
    echo '</table>' ;

    $result = $repoMy->sendError($message);
    die();
}
