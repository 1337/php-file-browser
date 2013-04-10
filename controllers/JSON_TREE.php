<?php
    /**
     * json tree page
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }

    // return JSON file list of a given folder.
    $files = $cwd->files((int)$param1); // defaults to show everything
    $output = array ();
    foreach ($files as $file) {
        if (is_file($file)) {
            $file_object = new FileTools ($cwd, $file);
            $output[] = array (
                'name' => basename($file_object),
                'path' => (string)$cwd,
                'type' => 'file',
                'size' => $file_object->natural_size(),
                'perm' => $file_object->perms()
            );
        } else if (is_dir($file)) {
            $dir_object = new DirTools ($file);
            $output[] = array (
                'name' => basename($dir_object),
                'path' => (string)$cwd,
                'type' => 'dir',
                'size' => '',
                'perm' => $dir_object->perms()
            );
        }
    }

    header("Content-type: application/json");
    echo json_encode($output);