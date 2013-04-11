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
        $c = vars('c' . $i); // these are not the same params as mode 4
        $f = vars('f' . $i);

        if ($c === '1') { // checkbox for this file is enabled
            try {
                fs_operation($act, $cwd . $f, new FileTools($cwd, $f));
            } catch (Exception $e) {
                die (strval($e));
            }
        }
    } while (vars('f' . $i));

    // redirect to the original tree page
    header("Location: " . str_replace('GROUP_ACTIONS', 'TREE', $_SERVER['REQUEST_URI']));