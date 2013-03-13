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
            'mode':'<?php echo $CONFIG['MODES']['JSON_TREE']; ?>',
            'cwd':path,
            'param1': <?php echo $CONFIG['DIRS_AND_FILES']; ?>
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
                '&mode=<?php echo $CONFIG['MODES']['EDITOR']; ?>',
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
