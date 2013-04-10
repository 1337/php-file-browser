<?php
    /**
     * group commands page
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    $i = 0;
    do {
        $i++;
        $param1 = vars('c' . $i); // these are not the same params as mode 4
        $param2 = vars('f' . $i);

        if ($param1 === '1') { // checkbox for this file is enabled
            switch (strtolower($a)) {
                case 'rm';
                case 'rmdir':
                    unlink($param2);
                    break;
                case 'archive':
                    $pcd = date('ymd');
                    mkdir("$cwd/archive.b$pcd/");
                    rename($param2, "$cwd/archive.b$pcd/" . basename($param2));
                default:
            }
        }
    } while (vars('c' . $i));

    $cf = basename($_SERVER['SCRIPT_FILENAME']);
    $pf = 'http://' . $_SERVER['SERVER_NAME'];

    // redirect to the original tree page
    header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=1");