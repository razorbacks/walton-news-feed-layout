<?php

namespace razorbacks\walton\news;

use Exception;

class Xsrf
{
    public static function verify()
    {
        if (empty($_POST['xsrf'])) {
            $error = 'ERROR: no xsrf token set in post';
            echo $error;
            throw new Exception($error);
        }

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
    }
}
