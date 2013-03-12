<html>
    <?php
        /**
         * editor page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        // if I need to open/save a file then show...
        if (!strlen($file) || !is_file($file)) {
            die ("To begin, click on a file name in the file panel.");
        } else {
            include_once('head.php');
            $content = file_get_contents($file);
        }
    ?>
    <body style='overflow: hidden;'>
        <form method='post'>
            <textarea class='php editor' style='width:100%;height:100%;'
                       name='content'
                       id='content'><?php echo htmlspecialchars($content); ?></textarea>
            <input type='hidden' name='cwd' value='<?php echo $cwd; ?>'/>
            <input type='hidden' name='file'
                    value='<?php echo $file_base; ?>'/>
            <input type='hidden' name='mode' value='2'/>
            <input type='submit' name='save' id='save'
                    value='Save' style='display:none'/>
        </form>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/lib/codemirror.js"></script>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/mode/xml/xml.js"></script>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/mode/javascript/javascript.js"></script>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/mode/css/css.js"></script>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/mode/clike/clike.js"></script>
        <script src="<?php echo CDN_ROOT; ?>scripts/codemirror/mode/php/php.js"></script>
        <script>
            $(document).ready(function () {
                var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                    lineNumbers:true,
                    theme:"monokai",
                    mode:"application/x-httpd-php",
                    indentUnit:4,
                    smartIndent:true,
                    tabSize:4,
                    indentWithTabs:false,
                    matchBrackets:true,
                    pollInterval:200,
                    undoDepth:999,
                    value: $('#content').val()
                });
            });
        </script>
    </body>
</html>