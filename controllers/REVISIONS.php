<?php
    include_once('simplediff.php');

    // return list of revisions of a given file.
    $revisions_list = $file->revisions();
    $revisions = array ();
    foreach ($revisions_list as $rev_str) {
        $rev = new FileTools($cwd, $rev_str);
        $new = $rev->contents();

        if (isset ($old_rev)) {
            $old = $old_rev->contents();
        } else {
            $old = $file->contents();
        }

        $revisions[] = array (
            'name' => basename($rev),
            'original_name' => $rev->original_filename(),
            'path' => (string)$cwd,
            'type' => 'file',
            'size' => $rev->natural_size(),
            'perm' => $rev->perms(),
            'icon' => $rev->icon(),

            'diff' => htmlDiff($new, $old),  // that's right, i swapped old and new
            'date' => date('l, Y-m-d h:i:s A', $rev->revision_time()),
            'stamp' => implode('', $rev->revision_stamp())
        );
        $old_rev = $rev;  // keep reference
    }