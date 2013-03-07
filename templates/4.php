<?php
    /**
     * commands page
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    // transform params
    $param1 = htmlspecialchars(urldecode($param1));
    $param2 = htmlspecialchars(urldecode($param2));

    switch (strtolower($act)) {
        case 'mv':
            rename($param1, $param2);
            break;
        case 'chmod':
            chmod($param1, $param2);
            break;
        case 'cp':
            copy($param1, $param2);
            break;
        case 'mkdir':
            mkdir($param1);
            break;
        case 'touch':
            touch($param1);
            break;
        case 'rm':
            unlink($param1);
            break;
        case 'rmdir':
            rmdir($param1);
            break;
        default:
            die("No such command: $act");
    }

    $cf = basename($_SERVER['SCRIPT_FILENAME']);
    $pf = 'http://' . $_SERVER['SERVER_NAME'];

    // redirect to the original tree page
    header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=1");