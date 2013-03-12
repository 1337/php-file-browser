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
            CDN_ROOT . 'scripts/codemirror/lib/codemirror.css',
            CDN_ROOT . 'scripts/codemirror/theme/monokai.css',
            CDN_ROOT . 'css/custom.css'
        );

        foreach ($css_files as $css) {
            echo "<link rel='stylesheet' type='text/css' href='$css' />";
        }
    ?>
    <script src='https://raw.github.com/1337/Lazyload/master/lazyload.min.js'></script>
    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
    <script>
        var populate_tree = populate_tree_ex = parent_path = null;
    </script>
</head>