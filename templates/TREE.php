<html>
    <?php
        /**
         * tree page
         */
        if (!defined('PROJECT_ROOT')) {
            die();
        }

        include_once('head.php');
        $c = 0;
        $f = 0;
    ?>
    <body class='tree'>
        <h2 id='filetree_head' class='header'>
            <span class="small"><?php echo dirname($cwd); ?>/</span>
            <br />
            <?php echo basename($cwd); ?>
            </h2>

        <form method='post' target='TREE' action='?mode=<?php mode('GROUP_ACTIONS') ?>'>
            <!-- ?mode=5 is needed -->
            <table id='filetree' class='fancy-table' cellspacing='0' cellpadding='2'>
                <tr>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Size</th>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="?mode=<?php mode('TREE') ?>&cwd=<?php echo dirname($cwd); ?>">
                        (Up one level)
                    </a></td>
                    <td></td>
                </tr>
            <?php foreach ($files as $file) {
                switch ($file['type']) {
                    case 'dir':
            ?>
                        <tr class="dir">
                            <!-- dir -->
                            <td style="text-align: right;"><img src='<?php echo $file['icon']; ?>' /></td>
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
                            <td style="text-align: right;">
                                <input type="checkbox" name="c<?php echo ++$c; ?>" value="1" />
                                <input type="hidden" name="f<?php echo $c; ?>"
                                       value="<?php echo $file['name']; ?>" />
                                <img src='<?php echo $file['icon']; ?>' />
                            </td>
                            <td class="clickable">
                                <a href="?mode=<?php mode('EDITOR'); ?>&cwd=<?php echo $cwd; ?>&file=<?php echo $file['name']; ?>"
                                   target="EDITOR">
                                    <?php echo $file['name']; ?>
                                </a>
                                <?php if ($file['revision_count']) {
                                    echo "<br>",
                                         "<a href='?mode=REVISIONS&cwd=$cwd&file=",
                                         $file['name'],
                                         "' target='EDITOR'",
                                          " class='small revisions'",
                                          ">See revisions</a>";
                                } ?>
                            </td>
                            <td style="text-align: right;"><?php echo $file['size']; ?></td>
                        </tr>
            <?php
                }
            }
            ?>
            </table>

            <div id="selected_items" style="display: none;">
                <p class='header'>Selected items</p>
                <label>
                    <input type='radio' name='act' value='rm'>
                    <b>Delete</b>
                    <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="small">Permanently remove selected files.</span>
                </label>
                <br/>
                <label>
                    <input type='radio' name='act' value='archive'>
                    <b>Archive</b>
                    <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="small">Move selected files to an archive directory.</span>
                </label>
                <br/>
                <br/>
                <input type='hidden' name='cwd'
                       value='<?php echo $cwd; ?>'/>
                <input type='hidden' name='mode'
                       value='<?php mode('GROUP_ACTIONS') ?>'/>
                <input type='submit'/>
            </div>
        </form>
        <div id="upload">
            <form method='post' target='TREE'
                  action='?mode=<?php mode('UPLOAD_HERE') ?>'
                  enctype='multipart/form-data'
                style='overflow: hidden;'>
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
        </div>
        <form method='post' target='TREE' action='?mode=<?php mode('COMMAND_LINE') ?>'>
            <!-- ?mode=COMMAND_LINE is needed -->
            <p class='header'>Execute</p>
            <table>
                <tr>
                    <td>Command:</td>
                    <td>
                        <!-- input type='text' name='act'/ -->
                        <select name="act" id="act"
                                style="width: 200px;">
                            <option value="chmod">chmod( p1, p2 )</option>
                            <option value="cp">cp( p1, p2 )</option>
                            <option value="mkdir">mkdir( p1 )</option>
                            <option value="mv">mv( p1, p2 )</option>
                            <option value="restore">restore( p1 )</option>
                            <option value="rm">rm( p1 )</option>
                            <option value="rmdir">rmdir( p1 )</option>
                            <option value="touch">touch( p1 )</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Param 1:</td>
                    <td>
                        <input type='text' id='param1'
                               name='param1' value='<?php echo $cwd; ?>'
                               style="width: 200px;" />
                    </td>
                </tr>
                <tr>
                    <td>Param 2:</td>
                    <td>
                        <input type='text' id='param2' name='param2'
                               style="width: 200px;" />
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
            $(document).ready(function () {
                $('#filetree input').click(function () {
                    var selectedItems = $('#selected_items');
                    if ($('#filetree input:checked').length) {
                        selectedItems.slideDown('slow');
                    } else {
                        selectedItems.slideUp('slow');
                    }
                });
            });
        </script>
    </body>
</html>