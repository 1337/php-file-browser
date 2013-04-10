<?php
    /**
     * init variables that all modes might require.
     * Modes (e.g. ?mode=TREE):
     * 0   frame (default)         -
     * 1   tree                    tree / editor
     * 2   editor                  editor
     * 3   download                _blank
     * 4   actions: commands       -
     * 5   actions: group actions  -
     * 6   current dir, download   -
     * 7   current dir, upload     tree
     * 8   login window (set cookies)
     * 9   ajax file transfer (accepts $_POST)
     * 10  ajax JSON filelist      tree
     * 99  debug
     */

    if (!defined('PROJECT_ROOT')) {
        die();
    }

    // populate useful variables
    $username = vars('username');
    $password = vars('password');

    $cwd = new DirTools(vars('cwd', getcwd()));
    $mode = vars('mode', 'FRAME');  // 0 = frame page
    $file = new FileTools($cwd, vars('file'));
    $file_base = basename($file);

    $act = vars('act');
    $param1 = vars('param1'); // params for $act
    $param2 = vars('param2');
