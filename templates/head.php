<?php
    if (!defined('PROJECT_ROOT')) {
        die();
    }
?>
<head>
    <?php
        $css_files = array (
            // 'css/opensans.css',
            'css/droidsans.css',
            'scripts/codemirror/lib/codemirror.css',
            'scripts/codemirror/theme/monokai.css',
            'css/custom.css');

        foreach ($css_files as $css) {
            echo "<link rel='stylesheet' type='text/css' href='$css' />";
        }
    ?>
    <script type="text/javascript" src='scripts/lazyload.min.js'></script>
    <script type="text/javascript" src='scripts/jquery.min.js'></script>
    <script type="text/javascript">
        var populate_tree = populate_tree_ex = parent_path = function () {};
    </script>
</head>