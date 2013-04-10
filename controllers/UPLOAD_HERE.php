<?php
    /**
     * upload mode
     * this provides no feedback, and overwrites any files.
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    if (isset ($_FILES['fileobj'])) {
        if (isset ($_POST['overwrite']) && $_POST['overwrite'] == '1') {
            // upload if file exists (too).
            move_uploaded_file($_FILES['fileobj']['tmp_name'],
                               "$cwd/" . $_FILES['fileobj']['name']);
        } else {
            // upload if file doesn't exist.
            if (!file_exists("$cwd/" . $_FILES['fileobj']['name'])) {
                move_uploaded_file($_FILES['fileobj']['tmp_name'],
                                   "$cwd/" . $_FILES['fileobj']['name']);
            }
        }
    }
    $cf = basename($_SERVER['SCRIPT_FILENAME']);
    $pf = 'http://' . $_SERVER['SERVER_NAME'];

    // redirect to the original tree page
    header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=TREE");
