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

        <form method='post' target='tree' action='?mode=<?php mode('GROUP_ACTIONS') ?>'>
            <!-- ?mode=5 is needed -->
            <table id='filetree' cellspacing='0' cellpadding='2'>
                <tr>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Size</th>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="?mode=<?php mode('TREE') ?>&cwd=<?php echo dirname($cwd); ?>">(Up a level)</a></td>
                    <td></td>
                </tr>
            <?php foreach ($files as $file) {
                switch ($file['type']) {
                    case 'dir':
            ?>
                        <tr class="dir">
                            <!-- dir -->
                            <td><input type="checkbox" /><img src='<?php echo $file['icon']; ?>' /></td>
                            <td class="clickable">
                                <a href="?mode=<?php mode('TREE'); ?>&cwd=<?php echo $cwd . $file['name']; ?>"
                                   target="TREE">
                                    <?php echo $file['name']; ?>
                                </a>
                            </td>
                            <td></td>
                        </tr>
            <?php
                        break;
                    case 'file':
                    default:
            ?>
                        <tr class="file">
                            <!-- file -->
                            <td><input type="checkbox" /><img src='<?php echo $file['icon']; ?>' /></td>
                            <td class="clickable">
                                <a href="?mode=<?php mode('EDITOR'); ?>&cwd=<?php echo $cwd; ?>&file=<?php echo $file['name']; ?>"
                                   target="EDITOR">
                                    <?php echo $file['name']; ?>
                                </a>
                                <?php if ($file['revision_count']) {
                                    echo "<br> See revisions";
                                } ?>
                            </td>
                            <td style="text-align: right;"><?php echo $file['size']; ?></td>
                        </tr>
            <?php
                }
            }
            ?>
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
                   value='<?php mode('GROUP_ACTIONS') ?>'/>
            <input type='submit'/>
        </form>
        <form method='post' target='tree'
              action='?mode=<?php mode('UPLOAD_HERE') ?>'
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
                               value='<?php mode('UPLOAD_HERE') ?>'/>
                        <input type='hidden' name='cwd'
                               value='<?php echo $cwd; ?>'/>
                        <input type='submit'/>
                    </td>
                </tr>
            </table>
        </form>
        <form method='post' target='tree' action='?mode=<?php mode('COMMAND_LINE') ?>'>
            <!-- ?mode=COMMAND_LINE is needed -->
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
                               value='<?php mode('COMMAND_LINE') ?>'/>
                        <input type='hidden' name='cwd'
                               value='<?php echo $cwd; ?>'/>
                        <input type='submit'/>
                    </td>
                </tr>
            </table>
        </form>
        <script type="text/javascript">
            $('.dir .clickable');
        </script>
    </body>
</html>