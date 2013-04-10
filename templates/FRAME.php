<?php
    /**
     * frame page
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }
?>
<html>
<frameset cols="300px,*">
    <frame name="tree" src="?mode=TREE"/>
    <frame name="editor" src="?mode=EDITOR"/>
</frameset>
<noframes></noframes>
</html>