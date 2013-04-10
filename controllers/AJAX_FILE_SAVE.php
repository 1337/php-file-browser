<?php
    /**
     * ajax file saving mode
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    // if I need to open/save a file then show...
    if (!strlen($file) || !is_file($file)) {
        if (vars('content')) { // save?
            $content = vars('content');
            $file_obj = new FileTools($cwd, $file);

            //pretend this is a backup
            if (BACKUP_BEFORE_SAVING) {
                $file_obj->backup();
            }

            if (file_put_contents($file, $content) === false) {
                echo ("Failed to save $file !");
            } else {
                echo ("Saved.");
            }
        }
    }