<?php
    /**
     * PHP File Browser (GPLv3) 2013 Brian Lai
     * Code IDE with built-in version tracking
     *
     * Do not edit this program with itself. It doesn't work that way!
     *
     * This is the entry point of the browser.
     * It decides which file to continue execution.
     *
    */

    define('PROJECT_ROOT', dirname(__FILE__));

    // will only bring in configs from files called this.
    // include prefix slash.
    define('CONFIG_FILENAME', '/config.php');

    // all config files should start with this line:
    // @include_once(dirname(__FILE__) . CONFIG_FILENAME); $CONFIG = array();
    // and end with this line:
    // $SETTINGS = array_merge_recursive($SETTINGS, $CONFIG);
    $SETTINGS = array (
        'VERSION' => 5.2,
        'HOSTNAME' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
        'BACKUP_BEFORE_SAVING' => true,
        'SHOW_HIDDEN_OBJECTS' => true, // unix only!
        'SHOW_BACKUP_OBJECTS' => false, // show .b??????.bak files
        'CHECK_PASSWORD' => false, // show login window
        'ALLOWED_USERS' => array (
            // 'brian' => '526242588032599f491f36c10137c88c076384ef',
            // 'guest' => '787373e81b9e76715abeae529faf9a0a9dbf5079'
        )
    );

    // initiate chain inclusion. parent config SUPERSEDES current config.
    include_once(PROJECT_ROOT . CONFIG_FILENAME);
    include_once(PROJECT_ROOT . '/lib.php');
    include_once(PROJECT_ROOT . '/init.php');

    // public subs ============================================================
    chdir($cwd); // because

    if ($SETTINGS['CHECK_PASSWORD'] === true && strlen($username) > 0) {
        // login request
        try {
            $hash = $config['ALLOWED_USERS'][$username];
            if (check_password($username, $password, $hash)) {
                setcookie("username", $username, time() + 36000);
                setcookie("password", $password, time() + 36000);
            } else {
                $mode = LOGIN; // authentication failed; redirect to login page
            }
        } catch (Exception $e) {
            $mode = LOGIN;
        }
    }

    // process the request. this is actually the controller.
    $controller_filename = PROJECT_ROOT . '/controllers/' . $mode . '.php';
    if (file_exists($controller_filename)) {
        include_once($controller_filename);
    }

    $template_filename = PROJECT_ROOT . '/templates/' . $mode . '.php';
    if (file_exists($template_filename)) {
        include_once($template_filename);
    }

    die();  // just in case