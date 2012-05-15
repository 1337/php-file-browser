// http://blog.fedecarg.com/2011/07/12/javascript-asynchronous-script-loading-and-lazy-loading/
var loader=function(a,b){b = b||function(){};for(var c=a.length,d=c,e=function(){
    if(!(this.readyState&&this.readyState!=="complete"&&this.readyState!=="loaded")){
    this.onload=this.onreadystatechange=null;--d||b()}},f=document.getElementsByTagName("head")[0],
    g=function(a){var b=document.createElement("script");b.async=true;
    b.src=a;b.onload=b.onreadystatechange=e;f.appendChild(b)};c;)g(a[--c])};

var populate_tree = null;
var populate_tree_ex = null;
var parent_path = null;

loader (['http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'], function () {
    $(document).ready (function () {
        var rot13 = function (s) {
            return s.replace(/[a-zA-Z]/g,function(c){
            return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);})
        };

        // global
        parent_path = function (path) {
            return path.substr(0, path.lastIndexOf('/'));
        }

        // global
        populate_tree_ex = function (id, path) {
            $.getJSON ('<?php echo basename (__FILE__); ?>', {
                    'mode': '<?php echo JSON_TREE; ?>',
                    'cwd': path,
                    'param1': <?php echo DIRS_AND_FILES; ?>
                }, function (data) {
                    populate_tree (id, data);
                }
            );
            $('#filetree_head').html ('<a href="#" onclick="javascript:populate_tree_ex(\'' + id + '\', \'' + parent_path (path) + '\');">' +
                "<img src='http://img707.imageshack.us/img707/1033/iconfolderup.gif' /" + ">" +
                "</a><b>" + path + "</b>"
            );
        };

        // global
        populate_tree = function (id, data) {
            var ctl = $('#' + id);
            var i = 0;
            var path_style = 'border-left: 3px <?php echo $config['HIGHLIGHT']; ?> solid; font-weight: bold;';
            var file_style = '';

            ctl.html (""); // clear it
            for (x in data) {
                i++;
                if (data[x].type == 'dir') { // folder
                    var fi = data[x].perm;
                    var isdir = true;
                    var link = '<a href="#" onclick="javascript:populate_tree_ex(\'' + id + '\', \'' + data[x].path + '/' + data[x].name + '\');">' +
                                   data[x].name +
                               '</a>';
                } else { // file
                    var fi = '<a href="?file=' + data[x].name + '&amp;mode=3">' + data[x].size + '</a>';
                    var isdir = false;
                    var link = '<a href="?cwd=' + data[x].path +
                                 '&amp;file=' + data[x].name +
                                 '&amp;mode=2" ' +
                                 'target="editor">' + data[x].name + '</a>';
                }
                ctl.append (
                    '<tr ' + (!(i % 2)? "style='background-color:rgba(255,255,255,0.1);'": '') + '>' +
                        '<td style="' + (isdir ? path_style : file_style) + '">' +
                            '<input type="checkbox" name="c' + i + '" value="1" /' + '>' +
                            '<input type="hidden" name="f' + i + '" ' +
                                   'value="' + data[x].path + '/' + data[x].name + '" /' + '>' +
                        '</td>' +
                        '<td style="width:100%;padding: 10px 3px 3px 6px;">' +
                            link +
                            '<span class="small" style="float: right">' +
                                fi +
                            '</span>' +
                        '</td>' +
                    '</tr>'
                );
            }
        };


        if ($('#filetree').length >= 1) {
            populate_tree_ex ('filetree', '<?php echo $cwd; ?>');
        }
    });
});
