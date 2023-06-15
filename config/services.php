<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Loader;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader = new Loader();

$loader->registerNamespaces(array(
    'Learncom\Models' => __DIR__ . '/../apps/models/',
    'Learncom\Repositories' => __DIR__ . '/../apps/repositories/',
    'Learncom\Utils' => __DIR__ . '/../apps/library/Utils/'
));



/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
	array(
		__DIR__ . '/../apps/library/',
	)
);
$loader->register();
/**
 * Cloud Flare Fix CUSTOMER IP
 */
function ip_in_range($ip, $range) {
    if (strpos($range, '/') !== false) {
        // $range is in IP/NETMASK format
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            // $netmask is a 255.255.0.0 format
            $netmask = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
            return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
        }
        else {
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while(count($x)<4) $x[] = '0';
            list($a,$b,$c,$d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);

            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32-$netmask)) - 1;
            $netmask_dec = ~ $wildcard_dec;

            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
    }
    else {
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !==false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (strpos($range, '-')!==false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec = (float)sprintf("%u",ip2long($lower));
            $upper_dec = (float)sprintf("%u",ip2long($upper));
            $ip_dec = (float)sprintf("%u",ip2long($ip));
            return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
        }
        return false;
    }
}
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $cf_ip_ranges = array('204.93.240.0/24',
        '204.93.177.0/24',
        '199.27.128.0/21',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2c0f:f248::/32',
        '2a06:98c0::/29');
    foreach ($cf_ip_ranges as $range) {
        if (ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            break;
        }
    }
}
/**
 * Read configuration
 */
$config = include __DIR__ . "/config.php";
/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name,
        'charset'=> $config->database->charset
    ));
};


/**
 * Registering a router
 */
$di['router'] = function () {
    $router = new Router();
	$router->removeExtraSlashes(true);
	$router->setDefaultModule("frontend");
	
	//Set 404 paths
    $router->notFound(array(
		"module" => "frontend",
		"controller" => "notfound",
		"action" => "index"
	));
	

    $router->add("/nodata", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "nodata"
    ));
    $router->add("/permission", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "alert"
    ));
    $router->add("/time", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "time"
    ));
    $router->add("/error", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "error"
    ));

    //Define a router index
    $router->add("/", array(
		"module" => "frontend",
        "controller" => "index",
        "action"     => "index"
    ));
    $router->add("/qr-code", array(
		"module" => "frontend",
        "controller" => "index",
        "action"     => "getqrcode"
    ));
    $router->add("/blogs", array(
        "module" => "frontend",
        "controller" => "blog",
        "action"     => "index"
    ));
    $router->add("/blogs/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "blog",
        "action"     => "detail"
    ));


	$router->add("/login", array(
		"module"	=> "frontend",
		"controller" => "login",
        "action"     => "login"
	));
    $router->add("/logout", array(
        "module"	=> "frontend",
        "controller" => "login",
        "action"     => "logout"
    ));
    $router->add("/register", array(
        "module"	=> "frontend",
        "controller" => "register",
        "action"     => "index"
    ));
    $router->add("/baigiang", array(
        "module"	=> "frontend",
        "controller" => "lesson",
        "action"     => "index"
    ));
    $router->add("/tailieu", array(
        "module"	=> "frontend",
        "controller" => "document",
        "action"     => "index"
    ));
    $router->add("/tailieuboiduong", array(
        "module"	=> "frontend",
        "controller" => "documentdifficult",
        "action"     => "index"
    ));
    $router->add("/khoahoc", array(
        "module"	=> "frontend",
        "controller" => "course",
        "action"     => "index"
    ));
    $router->add("/cron/updatesuccesstest", array(
        "module"	=> "frontend",
        "controller" => "cron",
        "action"     => "updatesuccesstest"
    ));


    //myaccount
    $router->add("/account/change-profile", array(
        "module"	=> "frontend",
        "controller" => "myaccount",
        "action"     => "changeprofile"
    ));
    $router->add("/account/change-pass", array(
        "module"	=> "frontend",
        "controller" => "myaccount",
        "action"     => "changepass"
    ));
    $router->add("/account/list-course", array(
        "module"	=> "frontend",
        "controller" => "myaccount",
        "action"     => "course"
    ));
    $router->add("/account/list-score", array(
        "module"	=> "frontend",
        "controller" => "myaccount",
        "action"     => "score"
    ));
	$router->add('/dashboard', array(
		"module" => "backend",
		"controller" => "index",
		"action"	=> "index"
	));
	
	$router->add('/dashboard/:controller', array(
		"module" => "backend",
		"controller" => 1,
		"action"	=> "index"
	));
    /*===========Page ============*/
    $router->add('/dashboard/list-page', array(
        "module" => "backend",
        "controller" =>'page',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-page', array(
        "module" => "backend",
        "controller" => 'page',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-page', array(
        "module" => "backend",
        "controller" => 'page',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-page', array(
        "module" => "backend",
        "controller" => 'page',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/translate-page', array(
        "module" => "backend",
        "controller" => 'page',
        "action"	=> "translate"
    ));
    /*======Role Controller*/
    $router->add('/dashboard/list-role', array(
        "module" => "backend",
        "controller" => "role",
        "action"	=> "read"
    ));
    $router->add('/dashboard/create-role', array(
        "module" => "backend",
        "controller" => "role",
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-role', array(
        "module" => "backend",
        "controller" => "role",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-role', array(
        "module" => "backend",
        "controller" => "role",
        "action"	=> "delete"
    ));
    /*======User Controller*/
    $router->add('/dashboard/list-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "read"
    ));
    $router->add('/dashboard/edit-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "delete"
    ));
    $router->add('/dashboard/reset-pass', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "reset"
    ));
    $router->add('/dashboard/create-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "create"
    ));

    /*===========List Type========= */
    $router->add('/dashboard/list-type', array(
        "module" => "backend",
        "controller" => 'type',
        "action"	=> "read"
    ));
    $router->add('/dashboard/create-type', array(
        "module" => "backend",
        "controller" => 'type',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-type', array(
        "module" => "backend",
        "controller" => 'type',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/insert-type', array(
        "module" => "backend",
        "controller" => 'type',
        "action"	=> "insert"
    ));
    $router->add('/dashboard/delete-type', array(
        "module" => "backend",
        "controller" => 'type',
        "action"	=> "delete"
    ));
    /*======Article Controller begin*/
    $router->add('/dashboard/list-article', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "read"
    ));
    $router->add('/dashboard/list-article-export', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "export"
    ));
    $router->add('/dashboard/create-article', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-article', array(
        "module" => "backend",
        "controller" => "article",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-article', array(
        "module" => "backend",
        "controller" => "article",
        "action"	=> "delete"
    ));

    /*========Upload File */
    $router->add('/dashboard/cloud-upload', array(
        "module" => "backend",
        "controller" => 'cloudupload',
        "action"	=> 'index'
    ));

    /*===========Banner ============*/
    $router->add('/dashboard/list-banner', array(
        "module" => "backend",
        "controller" =>'banner',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-banner', array(
        "module" => "backend",
        "controller" => 'banner',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-banner', array(
        "module" => "backend",
        "controller" => 'banner',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-banner', array(
        "module" => "backend",
        "controller" => 'banner',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-menu', array(
        "module" => "backend",
        "controller" =>'menu',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-menu', array(
        "module" => "backend",
        "controller" => 'menu',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-menu', array(
        "module" => "backend",
        "controller" => 'menu',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-menu', array(
        "module" => "backend",
        "controller" => 'menu',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-class', array(
        "module" => "backend",
        "controller" =>'class',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-class', array(
        "module" => "backend",
        "controller" => 'class',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-class', array(
        "module" => "backend",
        "controller" => 'class',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-class', array(
        "module" => "backend",
        "controller" => 'class',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-subject', array(
        "module" => "backend",
        "controller" =>'subject',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-subject', array(
        "module" => "backend",
        "controller" => 'subject',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-subject', array(
        "module" => "backend",
        "controller" => 'subject',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-subject', array(
        "module" => "backend",
        "controller" => 'subject',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-video', array(
        "module" => "backend",
        "controller" =>'video',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-video', array(
        "module" => "backend",
        "controller" => 'video',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-video', array(
        "module" => "backend",
        "controller" => 'video',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-video', array(
        "module" => "backend",
        "controller" => 'video',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-document', array(
        "module" => "backend",
        "controller" =>'document',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-document', array(
        "module" => "backend",
        "controller" => 'document',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-document', array(
        "module" => "backend",
        "controller" => 'document',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-document', array(
        "module" => "backend",
        "controller" => 'document',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-student', array(
        "module" => "backend",
        "controller" =>'student',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-student', array(
        "module" => "backend",
        "controller" => 'student',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-student', array(
        "module" => "backend",
        "controller" => 'student',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-student', array(
        "module" => "backend",
        "controller" => 'student',
        "action"	=> "delete"
    ));

    $router->add('/dashboard/list-teacher', array(
        "module" => "backend",
        "controller" =>'teacher',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-teacher', array(
        "module" => "backend",
        "controller" => 'teacher',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-teacher', array(
        "module" => "backend",
        "controller" => 'teacher',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-teacher', array(
        "module" => "backend",
        "controller" => 'teacher',
        "action"	=> "delete"
    ));

    $router->add('/dashboard/reset-student', array(
        "module" => "backend",
        "controller" => 'student',
        "action"	=> "reset"
    ));
    $router->add('/dashboard/list-config', array(
        "module" => "backend",
        "controller" =>'config',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-config', array(
        "module" => "backend",
        "controller" => 'config',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-config', array(
        "module" => "backend",
        "controller" => 'config',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-config', array(
        "module" => "backend",
        "controller" => 'config',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-chapter', array(
        "module" => "backend",
        "controller" =>'chapter',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-chapter', array(
        "module" => "backend",
        "controller" => 'chapter',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-chapter', array(
        "module" => "backend",
        "controller" => 'chapter',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-chapter', array(
        "module" => "backend",
        "controller" => 'chapter',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/get-chapter', array(
        "module" => "backend",
        "controller" => 'chapter',
        "action"	=> "getchapterbysubject"
    ));
    $router->add('/dashboard/list-summary', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "read"
    ));
    $router->add('/dashboard/view-summary', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "view"
    ));
    $router->add('/dashboard/test-summary', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "test"
    ));
    $router->add('/dashboard/detail-test', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "detailtest"
    ));
    $router->add('/dashboard/detail-list-test', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "listtest"
    ));
    $router->add('/dashboard/detail-user-test', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "detailtestuser"
    ));
    $router->add('/dashboard/delete-score', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "deletescore"
    ));
    $router->add('/dashboard/get-csv', array(
        "module" => "backend",
        "controller" => 'summary',
        "action"	=> "getcsv"
    ));
    /*===========Page Test============*/
    $router->add("/do-test", array(
        "module"	=> "frontend",
        "controller" => "test",
        "action"     => "index"
    ));
    $router->add("/start-test", array(
        "module"	=> "frontend",
        "controller" => "test",
        "action"     => "start"
    ));
    $router->add("/finish-test", array(
        "module"	=> "frontend",
        "controller" => "test",
        "action"     => "finish"
    ));
    $router->add("/view-test", array(
        "module"	=> "frontend",
        "controller" => "test",
        "action"     => "view"
    ));
    $router->add("/edit-data", array(
        "module"	=> "frontend",
        "controller" => "testnew",
        "action"     => "editdata"
    ));
    $router->add("/bat-dau", array(
        "module"	=> "frontend",
        "controller" => "testnew",
        "action"     => "index"
    ));
    $router->add("/kiem-tra", array(
        "module"	=> "frontend",
        "controller" => "testnew",
        "action"     => "start"
    ));
    $router->add("/ket-thuc", array(
        "module"	=> "frontend",
        "controller" => "testnew",
        "action"     => "finish"
    ));
    $router->add("/dap-an", array(
        "module"	=> "frontend",
        "controller" => "testnew",
        "action"     => "view"
    ));
    $router->add("/check-time", array(
        "module"	=> "frontend",
        "controller" => "test",
        "action"     => "checktime"
    ));
    $router->add('/dashboard/list-group', array(
        "module" => "backend",
        "controller" =>'group',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-group', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-group', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-group', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/get-group', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "getgroupbysubject"
    ));
    $router->add('/dashboard/get-group-test', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "getgroupbysubjecttest"
    ));
    $router->add('/dashboard/get-group-student', array(
        "module" => "backend",
        "controller" => 'group',
        "action"	=> "getgroupstudent"
    ));
    $router->add('/dashboard/list-videolesson', array(
        "module" => "backend",
        "controller" =>'videolesson',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-videolesson', array(
        "module" => "backend",
        "controller" => 'videolesson',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-videolesson', array(
        "module" => "backend",
        "controller" => 'videolesson',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-videolesson', array(
        "module" => "backend",
        "controller" => 'videolesson',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-test', array(
        "module" => "backend",
        "controller" =>'test',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-test', array(
        "module" => "backend",
        "controller" => 'test',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-test', array(
        "module" => "backend",
        "controller" => 'test',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-test', array(
        "module" => "backend",
        "controller" => 'test',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/detail-test-exam', array(
        "module" => "backend",
        "controller" => 'test',
        "action"	=> "detail"
    ));
    $router->add('/dashboard/edit-detail-test', array(
        "module" => "backend",
        "controller" => 'test',
        "action"	=> "editdetail"
    ));
    $router->add('/dashboard/upload-file', array(
        "module" => "backend",
        "controller" => 'cloudupload',
        "action"	=> "index"
    ));
    $router->add('/dashboard/update-subject', array(
        "module" => "backend",
        "controller" => 'cron',
        "action"	=> "index"
    ));
    $router->add('/dashboard/list-exam-type', array(
        "module" => "backend",
        "controller" =>'examtype',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-exam-type', array(
        "module" => "backend",
        "controller" => 'examtype',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-exam-type', array(
        "module" => "backend",
        "controller" => 'examtype',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-exam-type', array(
        "module" => "backend",
        "controller" => 'examtype',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/create-exam', array(
        "module" => "backend",
        "controller" => 'examtype',
        "action"	=> "createtest"
    ));
    $router->add('/dashboard/list-exam', array(
        "module" => "backend",
        "controller" => 'manageexam',
        "action"	=> "index"
    ));
    $router->add('/dashboard/delete-exam', array(
        "module" => "backend",
        "controller" => 'manageexam',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/accessdenied', array(
        "module" => "backend",
        "controller" => 'accessdenied',
        "action"	=> "read"
    ));
    $router->add('/dashboard/create-test-exam', array(
        "module" => "backend",
        "controller" => 'createtest',
        "action"	=> "create"
    ));
    $router->add('/dashboard/get-sum-exam', array(
        "module" => "backend",
        "controller" => 'createtest',
        "action"	=> "getsumexam"
    ));
    $router->add('/dashboard/list-exam-lesson', array(
        "module" => "backend",
        "controller" =>'examlesson',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-exam-lesson', array(
        "module" => "backend",
        "controller" => 'examlesson',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-exam-lesson', array(
        "module" => "backend",
        "controller" => 'examlesson',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-exam-lesson', array(
        "module" => "backend",
        "controller" => 'examlesson',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/get-exam-lesson', array(
        "module" => "backend",
        "controller" => 'examlesson',
        "action"	=> "getlesson"
    ));
    $router->add('/dashboard/list-exam-chapter', array(
        "module" => "backend",
        "controller" =>'examchapter',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-exam-chapter', array(
        "module" => "backend",
        "controller" => 'examchapter',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-exam-chapter', array(
        "module" => "backend",
        "controller" => 'examchapter',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-exam-chapter', array(
        "module" => "backend",
        "controller" => 'examchapter',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/get-exam-chapter', array(
        "module" => "backend",
        "controller" => 'examchapter',
        "action"	=> "getchapterbysubject"
    ));
    $router->add('/dashboard/list-exam-subject', array(
        "module" => "backend",
        "controller" =>'examsubject',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-exam-subject', array(
        "module" => "backend",
        "controller" => 'examsubject',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-exam-subject', array(
        "module" => "backend",
        "controller" => 'examsubject',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-exam-subject', array(
        "module" => "backend",
        "controller" => 'examsubject',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/get-exam-subject', array(
        "module" => "backend",
        "controller" => 'examsubject',
        "action"	=> "getsubject"
    ));
    $router->add('/dashboard/list-excellent-document', array(
        "module" => "backend",
        "controller" =>'excellentdocument',
        "action"	=>'read'
    ));
    $router->add('/dashboard/create-excellent-document', array(
        "module" => "backend",
        "controller" => 'excellentdocument',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-excellent-document', array(
        "module" => "backend",
        "controller" => 'excellentdocument',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-excellent-document', array(
        "module" => "backend",
        "controller" => 'excellentdocument',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-group-subject', array(
        "module" => "backend",
        "controller" => 'groupsubject',
        "action"	=> "read"
    ));
    $router->add('/dashboard/delete-group-subject', array(
        "module" => "backend",
        "controller" => 'groupsubject',
        "action"	=> "delete"
    ));
    $router->add('/office/list-new-class', array(
        "module" => "backend",
        "controller" =>'schoolclass',
        "action"	=>'read'
    ));
    $router->add('/office/create-new-class', array(
        "module" => "backend",
        "controller" => 'schoolclass',
        "action"	=> "create"
    ));
    $router->add('/office/edit-new-class', array(
        "module" => "backend",
        "controller" => 'schoolclass',
        "action"	=> "edit"
    ));
    $router->add('/office/delete-new-class', array(
        "module" => "backend",
        "controller" => 'schoolclass',
        "action"	=> "delete"
    ));
    $router->add('/office/list-new-test', array(
        "module" => "backend",
        "controller" =>'schooltest',
        "action"	=>'read'
    ));
    $router->add('/office/create-new-test', array(
        "module" => "backend",
        "controller" => 'schooltest',
        "action"	=> "create"
    ));
    $router->add('/office/edit-new-test', array(
        "module" => "backend",
        "controller" => 'schooltest',
        "action"	=> "edit"
    ));
    $router->add('/office/delete-new-test', array(
        "module" => "backend",
        "controller" => 'schooltest',
        "action"	=> "delete"
    ));
    $router->add('/office/list-new-student', array(
        "module" => "backend",
        "controller" =>'schoolstudent',
        "action"	=>'read'
    ));
    $router->add('/office/create-new-student', array(
        "module" => "backend",
        "controller" => 'schoolstudent',
        "action"	=> "create"
    ));
    $router->add('/office/edit-new-student', array(
        "module" => "backend",
        "controller" => 'schoolstudent',
        "action"	=> "edit"
    ));
    $router->add('/office/delete-new-student', array(
        "module" => "backend",
        "controller" => 'schoolstudent',
        "action"	=> "delete"
    ));
    $router->add('/office/detail-new-test-exam', array(
        "module" => "backend",
        "controller" => 'schooltest',
        "action"	=> "detail"
    ));
    $router->add('/office/edit-new-detail-test', array(
        "module" => "backend",
        "controller" => 'schooltest',
        "action"	=> "editdetail"
    ));
    $router->add('/office/student-new-test', array(
        "module" => "backend",
        "controller" => 'schoolmanage',
        "action"	=> "index"
    ));
    $router->add('/office/student-new-action', array(
        "module" => "backend",
        "controller" => 'schoolmanage',
        "action"	=> "read"
    ));
    $router->add('/office', array(
        "module" => "backend",
        "controller" => 'schoolindex',
        "action"	=> "index"
    ));
    $router->add('/office/new-list-score', array(
        "module" => "backend",
        "controller" => 'schoolscore',
        "action"	=> "index"
    ));
    $router->add('/office/new-view-score', array(
        "module" => "backend",
        "controller" => 'schoolscore',
        "action"	=> "view"
    ));
    $router->add('/office/new-delete-score', array(
        "module" => "backend",
        "controller" => 'schoolscore',
        "action"	=> "delete"
    ));
    $router->add('/office/new-csv-score', array(
        "module" => "backend",
        "controller" => 'schoolscore',
        "action"	=> "getcsv"
    ));
    $router->add('/office/new-detail-score', array(
        "module" => "backend",
        "controller" => 'schoolscore',
        "action"	=> "detail"
    ));
    $router->add('/office/list-new-exam-test', array(
        "module" => "backend",
        "controller" =>'schoolexamtest',
        "action"	=>'read'
    ));
    $router->add('/office/create-new-exam-test', array(
        "module" => "backend",
        "controller" => 'schoolexamtest',
        "action"	=> "create"
    ));
    $router->add('/office/edit-new-exam-test', array(
        "module" => "backend",
        "controller" => 'schoolexamtest',
        "action"	=> "edit"
    ));
    $router->add('/office/delete-new-exam-test', array(
        "module" => "backend",
        "controller" => 'schoolexamtest',
        "action"	=> "delete"
    ));
	$router->handle();
    return $router;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function () {
    $session = new SessionAdapter();
    ini_set('session.gc_maxlifetime', 10000);
    ini_set('session.gc_divisor', 5400);
    $session->start();
    return $session;
};
/**
 * Register My component
 */
$di->set('my', function(){
    return new \My();
});

/**
 * Register GlobalVariable component
 */
$di->set('globalVariable', function(){
    return new \GlobalVariable();
});

/**
 * Register cookie
 */
$di->set('cookies', function() {
    $cookies = new \Phalcon\Http\Response\Cookies();
	$cookies->useEncryption(false);
    return $cookies;
}, true);

/**
 * Register key for cookie encryption
 */
$di->set('crypt', function() {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey('binmedia123@@##'); //Use your own key!
    return $crypt;
});

/**
 * Register models manager
 */
$di->set('modelsManager', function() {
      return new Manager();
});