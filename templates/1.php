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
        <p id='filetree_head' class='header'></p>

        <form method='post' target='tree' action='?mode=5'>
            <!-- ?mode=5 is needed -->
            <table id='filetree' cellspacing='0' cellpadding='2'></table>
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
                   value='<?php echo GROUP_ACTIONS; ?>'/>
            <input type='submit'/>
        </form>
        <form method='post' target='tree'
              action='?mode=<?php echo UPLOAD_HERE; ?>'
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
                               value='<?php echo UPLOAD_HERE; ?>'/>
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
                               value='<?php echo COMMAND_LINE; ?>'/>
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
        <script type="text/javascript">
            "use strict";
            $(document).ready(function () {
                var rot13 = function (s) {
                    return s.replace(
                            /[a-zA-Z]/g,
                            function (c) {
                                return String.fromCharCode(
                                        (c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ?
                                                c :
                                                c - 26
                                );
                            }
                    );
                };

                // global scope
                parent_path = function (path) {
                    return path.substr(0, path.lastIndexOf('/'));
                }

                // global scope
                populate_tree_ex = function (id, path) {
                    $.getJSON('<?php echo basename(__FILE__); ?>', {
                                'mode':'<?php echo JSON_TREE; ?>',
                                'cwd':path,
                                'param1': <?php echo DIRS_AND_FILES; ?>
                            }, function (data) {
                                populate_tree(id, data);
                            }
                    );
                    var a = $('<a />', {
                        'href':'#',
                        'click':function () {
                            populate_tree_ex(id, parent_path(path));
                        },
                        'html':"<img src='http://i.imgur.com/gMwUw.gif' />"
                    });
                    $('#filetree_head')
                            .html('')
                            .append(a)
                            .append("<b>" + path + "</b>");
                };

                // global scope
                populate_tree = function (id, data) {
                    var ctl = $('#' + id);

                    ctl.html(''); // clear it
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].type == 'dir') { // folder
                            var fi = data[i].perm;
                            var isdir = true;
                            var path = data[i].path;
                            var link = $('<a />', {
                                'click':function () {
                                    populate_tree_ex(
                                            id,
                                            $(this).data('path') + $(this).data('name')
                                    );
                                },
                                'text':data[i].name,
                                'data':{
                                    'path':data[i].path,
                                    'name':data[i].name
                                }
                            });
                        } else { // file
                            var fi = '<a href="?cwd=' + data[i].path +
                                    '&file=' + data[i].name +
                                    '&mode=3">' + data[i].size +
                                    '</a>';
                            var isdir = false;
                            var link = $('<a />', {
                                'target':'editor',
                                'href':'?cwd=' + data[i].path +
                                        '&file=' + data[i].name +
                                        '&mode=<?php echo EDITOR; ?>',
                                'text':data[i].name
                            });
                        }
                        var td1 = $('<td class="' + (isdir ? 'path_style' : 'file_style') + '">' +
                                '<input type="checkbox" name="c' + i + '" value="1" />' +
                                '<input type="hidden" name="f' + i + '" ' +
                                'value="' + data[i].path + data[i].name + '" /></td>');
                        var td2 = $('<td />');

                        td2.append(link)
                                .append('<span class="small rfloat">' +
                                fi +
                                '</span>');
                        $('<tr />')
                                .append(td1)
                                .append(td2)
                                .appendTo(ctl);
                    }
                };

                populate_tree_ex('filetree', '<?php echo $cwd; ?>');
            });
        </script>
    </body>
</html>