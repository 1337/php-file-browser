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

            //pretend this is a backup
            if (BACKUP_BEFORE_SAVING) {
                if (!file_exists($file->backup_file())) {
                    // copy only if not exists (saves first file of date)
                    copy($file, $file->backup_file());
                }
                // inherit file permissions
                chmod($file->backup_file(), fileperms(__FILE__));
            }

            if (file_get_contents($file, $content) === false) {
                echo ("Failed to save $file !");
            } else {
                echo ("Saved.");
            }
        }
    }