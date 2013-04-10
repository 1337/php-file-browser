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
    <frame name="TREE" src="?mode=TREE"/>
    <frame name="EDITOR" src="?mode=EDITOR"/>
</frameset>
<noframes></noframes>
</html>