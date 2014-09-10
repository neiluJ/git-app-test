<?php
require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Components\ErrorReporterListener;
use Fwk\Core\Components\Descriptor\Descriptor;

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

// echo "Old: ". $_SERVER['REQUEST_URI'] ."<br />";
// echo "PHP_SELF: ". $_SERVER['PHP_SELF'] ."<br />";
/*
if (strpos($_SERVER['REQUEST_URI'], 'hhvm') !== false) {
    $_SERVER['PHP_SELF'] = "/~neiluj/test-git/public/";
    $_SERVER['REQUEST_URI'] = "/~neiluj/test-git/public/". substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], 'index.php'));
}

echo "New: ". $_SERVER['REQUEST_URI'] ."(". $_SERVER['SCRIPT_FILENAME'] .")<br />";
*/
$desc = new Descriptor(__DIR__ .'/../app/fwk.xml');
$response = $desc->execute('TestGit')
    ->addListener(new ErrorReporterListener())
    ->setDefaultAction('Home')
    ->run();

if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
    $response->send();
}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start),2);

//ob_end_flush();