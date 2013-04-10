(function ($, config) {
    "use strict";
    
    var parentPath, populateTreeEx, populateTree, rot13;
    
    rot13 = function (s) {
        return s.replace(
            /[a-zA-Z]/g,
            function (c) {
                return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
            }
        );
    };

    parentPath = function (path) {
        // does NOT really return
        return path.substr(0, path.lastIndexOf('/'));
    };

    populateTreeEx = function (targetElem, path) {
        // I'm guessing this is for starting the whole process?
        $.getJSON(config.__FILE__, {
            'mode': config.JSON_TREE,
            'cwd': path,
            'param1': config.DIRS_AND_FILES
        }, function (data) {
            populateTree(targetElem, data);
        });

        var a = $('<a />', {
            href: '?cwd=' + path /* '#' */,
            data: {
                'path': path
            }
            /*'click':function () {
            populateTreeEx(id, parentPath(path));
            }*/,
            'html':"<img src='http://i.imgur.com/gMwUw.gif' />"
        });
        $('#filetree_head')
            .html('')
            .append(a)
            .append("<b>" + path + "</b>");
    };

    populateTree = function (ctl, data) {
        ctl.html(''); // clear it
        for (var i = 0; i < data.length; i++) {
            if (data[i].type == 'dir') { // folder
                var fi = data[i].perm;
                var isdir = true;
                var path = data[i].path;
                var link = $('<a />', {
                    'click':function () {
                        populateTreeEx(
                            id,
                            $(this).data('path') + $(this).data('name'));
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
                    '&mode=' + config.EDITOR,
                    'text':data[i].name
                });
            }
            var td1 = $('<td class="' + (isdir ? 'path_style' : 'file_style') + '">' +
                '<input type="checkbox" name="c' + i + '" value="1" />' +
                '<input type="hidden" name="f' + i + '" ' +
                                'value="' + data[i].path + data[i].name + '" /></td>');
            var td2 = $('<td />');
            td2.append(link).append('<span class="small rfloat">' + fi + '</span>');
            $('<tr />').append(td1).append(td2).appendTo(ctl);
        }
    };
    $(document).ready(function () {
        populateTreeEx($('#filetree'), config.cwd);
    });

}(jQuery, CONFIG));