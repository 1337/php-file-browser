<html>
    <?php
        /**
         * tree page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        include_once('head.php');
    ?>
    <body class='tree'>
        <p id='filetree_head' class='header'><?php echo $cwd; ?></p>

        <form method='post' target='tree' action='?mode=5'>
            <!-- ?mode=5 is needed -->
            <table id='filetree' cellspacing='0' cellpadding='2'>
            <?php foreach ($files as $file) {
                switch ($file['type']) {
                    case 'dir':
            ?>
                <!-- dir -->

            <?php  case 'file':
                    default: ?>
                <!-- file -->
            <?php }} ?>
            </table>
            <p class='header'>Selected items</p>
            <label>
                <input type='radio' name='act' value='rm'> Delete
            </label>
            <br/>
            <label>
                <input type='radio' name='act' value='archive'> Archive
            </label>
            <br/>
            <br/>
            <input type='hidden' name='cwd'
                   value='<?php echo $cwd; ?>'/>
            <input type='hidden' name='mode'
                   value='<?php echo $CONFIG['MODES']['GROUP_ACTIONS']; ?>'/>
            <input type='submit'/>
        </form>
        <form method='post' target='tree'
              action='?mode=<?php echo $CONFIG['MODES']['UPLOAD_HERE']; ?>'
              enctype='multipart/form-data'>
            <!-- ?mode=7 is needed -->
            <p class='header'>Upload</p>
            <table>
                <tr>
                    <td>File:</td>
                    <td><input type='file' name='fileobj'/></td>
                </tr>
                <tr>
                    <td>Overwrite?</td>
                    <td>
                        <input type='checkbox' id='overwrite'
                               name='overwrite' value='1'/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='hidden' name='mode'
                               value='<?php echo $CONFIG['MODES']['UPLOAD_HERE']; ?>'/>
                        <input type='hidden' name='cwd'
                               value='<?php echo $cwd; ?>'/>
                        <input type='submit'/>
                    </td>
                </tr>
            </table>
        </form>
        <form method='post' target='tree' action='?mode=4'>
            <!-- ?mode=4 is needed -->
            <p class='header'>Execute</p>
            <table>
                <tr>
                    <td>Command:</td>
                    <td>
                        <input type='text' name='act'/>
                    </td>
                </tr>
                <tr>
                    <td>Param 1:</td>
                    <td>
                        <input type='text' id='param1'
                               name='param1' value='<?php echo $cwd; ?>'/>
                    </td>
                </tr>
                <tr>
                    <td>Param 2:</td>
                    <td>
                        <input type='text' id='param2' name='param2'/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type='hidden' name='mode'
                               value='<?php echo $CONFIG['MODES']['COMMAND_LINE']; ?>'/>
                        <input type='hidden' name='cwd'
                               value='<?php echo $cwd; ?>'/>
                        <input type='submit'/>
                    </td>
                </tr>
            </table>
        </form>
        <p>Commands: chmod(param1,param2), cp(param1,param2),
            mkdir(param1), mv(param1,param2),
            rmdir(param1), touch(param1)</p>
    </body>
</html>