<?php
    // return JSON file list of a given folder.
    $_files = $cwd->files((int)$param1); // defaults to show everything
    $files = array ();
    foreach ($_files as $file) {
        if (is_file($file)) {
            $file_object = new FileTools ($cwd, $file);
            $files[] = array (
                'name' => basename($file_object),
                'path' => (string)$cwd,
                'type' => 'file',
                'size' => $file_object->natural_size(),
                'perm' => $file_object->perms()
            );
        } else if (is_dir($file)) {
            $dir_object = new DirTools ($file);
            $files[] = array (
                'name' => basename($dir_object),
                'path' => (string)$cwd,
                'type' => 'dir',
                'size' => '',
                'perm' => $dir_object->perms()
            );
        }
    }

