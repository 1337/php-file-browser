<html>
    <?php
        /**
         * revisions page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        // if I need to open/save a file then show...
        if (sizeof ($revisions) <= 0) {
            die ();
        }

        include_once('head.php');
    ?>
    <body class="tree" style='overflow: hidden;'>
        <div id="success_message" class="overlay">File restored.</div>
        <div id="error_message" class="overlay">File NOT restored!</div>
        <p class="header">Select a revision to restore.</p>
        <div class="container" style="margin: 0 20px">
            <p>&nbsp;</p>
            <p>This will delete the current copy of your document.
               It cannot be undone.</p>
            <p>&nbsp;</p>

            <table class="revision fancy-table"
                   style="width: 80%;
                          margin: 0 auto;">
                <tr>
                    <th>File Name</th>
                    <th>Date</th>
                    <th>Size</th>
                    <th>Restore?</th>
                </tr>
                <?php
                    foreach ($revisions as $revision) {
                        echo "<tr>",
                            "<th >",
                            $revision['original_name'],
                            "</th>",
                            "<td data-stamp='",
                                $revision['stamp'],
                            "'>",
                            $revision['date'],
                            "</td>",
                            "<td>",
                            $revision['size'],
                            "</td>",
                            "<td>",
                            "<a class='restorable' href='#'>Restore</a>",
                            "</td>",
                            "</tr>",
                            "<tr>",
                            "<td colspan='4' style='text-align: left;'><code>",
                            $revision['diff'],
                            "</code></td>",
                            "</tr>";
                    }
                ?>
            </table>
            <p>&nbsp;</p>
            <p>Powered by (modified) <a href="https://github.com/paulgb/simplediff">SimpleDiff</a> by Paul Butler</p>
        </div>
        <script type="text/javascript">
            var backToEditor = function () {
                // go back to the editor for the same file
                window.location.href = window.location.href.replace('REVISIONS', 'EDITOR');
            };

            $('.restorable').click(function () {
                // too messy to handle with just html.
                $.ajax({
                    url: '?mode=COMMAND_LINE&cwd=<?php echo $cwd; ?>&file=' +
                         $(this).parent().parent().find('th').text() + // file name
                         '&act=restore&param1=' +
                         $(this).parent().parent().find('td').eq(0).data('stamp'),  // file stamp
                    success: function (data) {
                        if (data === 'ok') {
                            $('#success_message').fadeIn().delay(1500).fadeOut(backToEditor);
                        } else {
                            $('#error_message').fadeIn().delay(1500).fadeOut(backToEditor);
                        }
                    }
                });
            });
        </script>
    </body>
</html>