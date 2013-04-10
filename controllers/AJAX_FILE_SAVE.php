<?php
    /**
     * ajax file saving mode
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    // if I need to open/save a file then show...
    if (strlen($file) && is_file($file)) {
        if (vars('content')) { // save?
            $content = vars('content');
            $file_obj = new FileTools($cwd, $file);

            //pretend this is a backup
            if ($SETTINGS['BACKUP_BEFORE_SAVING']) {
                $file_obj->backup();
            }

            /*
            if (file_put_contents($file, $content) === false) {
                echo ("Failed to save $file !");
            } else {
                echo ("Saved.");
            }
            */
            file_put_contents($file, $content);
        }
    }

    $cf = basename($_SERVER['SCRIPT_FILENAME']);
    $pf = 'http://' . $_SERVER['SERVER_NAME'];

    // redirect to the original editor page
    header("location: ?cwd=$cwd&file=$file_base&mode=EDITOR");