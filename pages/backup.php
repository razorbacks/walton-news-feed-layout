<?php

use jpuck\Error\Handler;
use razorbacks\walton\news\Backup;
use razorbacks\walton\news\Scheduler;

require_once __DIR__.'/../vendor/autoload.php';

Handler::convertErrorsToExceptions();
Handler::swift();

if (empty($_POST['xsrf'])) {
    $error = 'ERROR: no xsrf token set in post';
    echo $error;
    throw new Exception($error);
}

if (empty($_POST['return'])) {
    $error = 'ERROR: no return set in post';
    echo $error;
    throw new Exception($error);
}

session_start();

if (empty($_SESSION['xsrf'])) {
    $error = 'ERROR: no xsrf token set in session';
    echo $error;
    throw new Exception($error);
}

if ($_POST['xsrf'] !== $_SESSION['xsrf']) {
    $error = 'ERROR: xsrf token mismatch';
    echo $error;
    throw new Exception($error);
}

$backup = new Backup;
$backup->setScheduler(new Scheduler);
$backup->save();

header("Location: $_POST[return]");
