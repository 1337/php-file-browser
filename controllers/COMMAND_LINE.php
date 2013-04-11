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

    try {
        fs_operation($act, $param1, $param2, $file);
    } catch (Exception $e) {
        die (strval($e));
    }

    // redirect to the original tree page
    header("Location: " . str_replace('COMMAND_LINE', 'TREE', $_SERVER['REQUEST_URI']));