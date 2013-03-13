<?php
    // all config files should start with this line:
    // @include_once(dirname(__FILE__) . CONFIG_FILENAME); $CONFIG = array();
    // and end with this line:
    // $SETTINGS = array_merge_recursive($SETTINGS, $CONFIG);

    @include_once(dirname(__FILE__) . CONFIG_FILENAME);
    $CONFIG = array ();

    $CONFIG = array (
        'CDN_ROOT' => 'https://raw.github.com/1337/php-file-browser/master/',
        'MODES' => array (
            'FRAME' => 0,
            'TREE' => 1,
            'EDITOR' => 2,
            'DOWNLOAD' => 3,
            'COMMAND_LINE' => 4,
            'GROUP_ACTIONS' => 5,
            'DOWNLOAD_HERE' => 6,
            'UPLOAD_HERE' => 7,
            'LOGIN' => 8,
            'AJAX_FILE_SAVE' => 9,
            'JSON_TREE' => 10,
            'DEBUG' => 99),
        'DIRS_ONLY' => 0,
        'FILES_ONLY' => 1,
        'DIRS_AND_FILES' => 2
    );

    $SETTINGS = array_merge_recursive($SETTINGS, $CONFIG);