<html>
    <?php
        /**
         * editor page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        // if I need to open/save a file then show...
        if ($file->size() <= 0) {
            die (file_get_contents(PROJECT_ROOT . '/templates/EDITOR_NULL.php'));
        } else {
            include_once('head.php');
            $content = file_get_contents($file);
        }
    ?>
    <body style='overflow: hidden;'>
        <link rel="stylesheet" href="scripts/codemirror-ui/css/codemirror-ui.css" type="text/css" media="screen" />
        <form method='post' id='editor_form' action="?mode=AJAX_FILE_SAVE">
            <textarea class='php editor' style='width:100%;height:100%;'
                       name='content'
                       id='content'><?php echo htmlspecialchars($content); ?></textarea>
            <input type='hidden' name='cwd' value='<?php echo $cwd; ?>'/>
            <input type='hidden' name='file'
                    value='<?php echo $file_base; ?>'/>
            <input type='hidden' name='mode' value='AJAX_FILE_SAVE'/>
            <input type='submit' name='save' id='save'
                    value='Save' style='display:none'/>
        </form>
        <script src="scripts/codemirror/lib/codemirror.js"></script>
        <script src="scripts/codemirror/mode/xml/xml.js"></script>
        <script src="scripts/codemirror/mode/javascript/javascript.js"></script>
        <script src="scripts/codemirror/mode/css/css.js"></script>
        <script src="scripts/codemirror/mode/python/python.js"></script>
        <script src="scripts/codemirror/mode/htmlmixed/htmlmixed.js"></script>
        <script src="scripts/codemirror/mode/clike/clike.js"></script>
        <script src="scripts/codemirror/mode/php/php.js"></script>
        <script src="scripts/codemirror-ui/js/codemirror-ui.js"></script>
        <script>
            $(document).ready(function () {
                /* var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                    lineNumbers:true,
                    theme:"monokai",
                    mode: "<?php echo $file->codemirror_mode(); ?>",
                    indentUnit:4,
                    smartIndent:true,
                    tabSize:4,
                    indentWithTabs:false,
                    matchBrackets:true,
                    pollInterval:200,
                    undoDepth:999,

                    path : 'scripts/codemirror-ui/js/',
                    searchMode: 'popup',
                    buttons : ['undo','redo','jump','reindent','about'],

                    value: $('#content').val()
                }); */

                var editor = new CodeMirrorUI(
                        document.getElementById("content"),
                        {
                            path : 'scripts/codemirror-ui/js/',
                            searchMode: 'popup',
                            buttons : [
                                'save', 'undo', 'redo', 'jump', 'reindent'
                            ]
                        },{
                            lineNumbers:true,
                            theme:"monokai",
                            mode: "<?php echo $file->codemirror_mode(); ?>",
                            indentUnit:4,
                            smartIndent:true,
                            tabSize:4,
                            indentWithTabs:false,
                            matchBrackets:true,
                            pollInterval:200,
                            undoDepth:999,
                            value: $('#content').val(),
                            extraKeys: {
                                "Ctrl-S": function(instance) {
                                    $('#editor_form').submit();
                                },
                                "Ctrl-/": "undo"
                            }
                        }
                );
            });
        </script>
    </body>
</html>