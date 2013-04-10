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
    <frame name="tree" src="?mode=1"/>
    <frame name="editor" src="?mode=2"/>
</frameset>
<noframes></noframes>
</html>