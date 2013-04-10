<?php
    /**
     * download page
     * (will fail if available server RAM < filesize)
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    header("Content-type: application/force-download");
    header("Content-Disposition: attachment; filename=\"$file_base\"");
    readfile($file); // chuck it out
    exit();