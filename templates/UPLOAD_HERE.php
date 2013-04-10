<html>
    <?php
        /**
         * download-only display page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        include_once('head.php');

        $files = $cwd->files(FILES_ONLY);
    ?>
    <body>
        <p class='header'>
            <b><?php echo basename((string)$cwd); ?></b>
        </p>
        <table class='filetree' cellspacing='0' cellpadding='2'>");
            <?php foreach ($files as $idx => $file) {
                if (!$pn->is_hidden()) { // don't show hidden files
                    echo "<tr><td style='width:100%%;'>
                              <a href='?file=$file&amp;mode=",
                         mode('DOWNLOAD', false),
                         "' target='_blank'>$file</a></td></tr>";
                }
            } ?>
        </table>
    </body>
</html>