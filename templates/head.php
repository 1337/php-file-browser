<?php
    if (!defined('PROJECT_ROOT')) {
        die();
    }
?>
<head>
    <?php
        $css_files = array (
            'http://fonts.googleapis.com/css?family=Open+Sans',
            'http://fonts.googleapis.com/css?family=Droid+Sans+Mono',
            'scripts/codemirror/lib/codemirror.css',
            'scripts/codemirror/theme/monokai.css',
            'css/custom.css');

        foreach ($css_files as $css) {
            echo "<link rel='stylesheet' type='text/css' href='$css' />";
        }
    ?>
    <script type="text/javascript"
             src='https://raw.github.com/1337/Lazyload/master/lazyload.min.js'></script>
    <script type="text/javascript"
             src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
    <script>
        var populate_tree = populate_tree_ex = parent_path = function () {};
    </script>
</head>