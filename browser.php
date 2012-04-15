<?php
    /*  PHP File Browser (GPLv3) 2012 Brian Lai
        Code IDE with built-in version tracking

        Do not edit this script with itself.
    */

    // settings ===============================================================
    $config = array (
        'VERSION' => 5.1,
        'HOSTNAME' => gethostbyaddr ($_SERVER['REMOTE_ADDR']),
        'HIGHLIGHT' => '#3399ee', // any HTML colour will do
        'BACKUP_BEFORE_SAVING' => true,
        'SHOW_HIDDEN_OBJECTS' => true, //only checks if objects' names begin with '.'
        'SHOW_BACKUP_OBJECTS' => false, //remove .b??????.bak files from the list
        'CHECK_PASSWORD' => false, //show login window if...
        'ALLOWED_USERS' => array (
            // user => sha1 hash of the password
            'brian' => '526242588032599f491f36c10137c88c076384ef',
            'guest' => '787373e81b9e76715abeae529faf9a0a9dbf5079'
        )
    );

    // functions ==============================================================
    function vars ($index = false, $default = null) {
        // gathers everything from the request.
        // see cached version of this function: github.com/1337/pop
        @session_start ();
        $vars = (array) array_merge (
            (array) $_COOKIE,
            (array) $_SESSION,
            (array) $_POST,
            (array) $_GET
        );
        if ($index === false) {
            return $vars; // return cache if it exists
        }
        if (isset ($vars[$index])) {
            return $vars[$index];
        }
        // everyone else would have returned by now
        return $default;
    }

    function filelist ($base, $what = 2) {
        /*  what
            0 = dirs only
            1 = files only
            2 = everything  */
        $da = array();
        $mdr = opendir($base);                  // open this directory
        while($fn = readdir($mdr)) {            // get each entry
            if (is_dir ($fn)) {
                if (($what == 0 || $what == 2) &&
                    $fn != '.' &&
                    $fn != '..') {
                    if (SHOW_HIDDEN_OBJECTS || substr ($fn,0,1) != '.') {
                        $da[] = $fn;
                    }
                }
            } else if (is_file ($fn)) {
                if ($what == 1 || $what == 2) {
                    if (SHOW_HIDDEN_OBJECTS || substr ($fn,0,1) != '.') {
                        $da[] = $fn;
                    }
                }
            }
        }
        closedir ($mdr); // close directory
        $index_count = sizeof ($da); // count elements in array
        if ($index_count > 0) {
            sort ($da); // sort will explode if count=0
            if (SHOW_BACKUP_OBJECTS != true) {
                $da = array_filter ($da, "filterbackupobjects");
            }
        }
        return $da;
    }

    function filterbackupobjects($var) {
        return !(substr ($var, -4, 4) == '.bak');
    }

    function extension ($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    function fileperm ($filename) {
        return substr(sprintf('%o', fileperms($filename)), -4);
    }

    function filesize_natural ($bytes) {
        # Snippet from PHP Share: http://www.phpshare.org
        if ($bytes >= 1073741824) {
            $bytes = number_format ($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format ($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format ($bytes / 1024, 2) . ' KB';
        } else {
            $bytes = $bytes . ' B';
        }
        return $bytes;
    }

    // public subs ============================================================
    $act = vars ('act');
    $cwd = vars ('cwd', getcwd());
    $mode = vars ('mode', 0); // 0 = frame page
    $file = vars ('file');
    $p1 = vars ('p1'); // params for $act
    $p2 = vars ('p2');
    $username = vars ('username');
    $password = vars ('password');

    // add user / sha1(pass) combinations here.
    if ($config['CHECK_PASSWORD'] === true) {
        if (strlen ($username) > 0) { // login request
            if (array_key_exists ($username, $config['ALLOWED_USERS']) &&
               (sha1 ($password) === $config['ALLOWED_USERS'][$username])) { // basically, password check
                setcookie ("username", $username, time() + 36000);
                setcookie ("password", $password, time() + 36000);
            } else {
                $mode = 8; // wrong password, switch to mode 8 (login window)
            }
        } else {
            if (isset ($_COOKIE["username"]) && isset ($_COOKIE["password"]) &&
                array_key_exists ($_COOKIE["username"], $config['ALLOWED_USERS']) &&
                $config['ALLOWED_USERS'][$_COOKIE["username"]] == sha1 ($_COOKIE["password"])) {
                // do nothing. user is authenticated.
            } else {
                // user not logged in or password is wrong
                $mode = 8; // switch to mode 8 (login window)
            }
        }
    }

    chdir ($cwd); // because

    /*  modes
        0   frame                   -
        1   tree                    tree / editor
        2   editor                  editor
        3   download                _blank
        4   actions: commands       -
        5   actions: group actions  -
        6   current dir, download   -
        7   current dir, upload     tree
        8   login window (set cookies)
        9   ajax file transfer (accepts $_POST)
        10  ajax JSON filelist      tree
        99    debug
    */
    if (in_array ($mode, array (0, 1, 2, 6)) { // modes with html heads
?>
        <html>
            <head>
                <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
                <link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'>
                <link href='http://ohai.ca/scripts/codemirror/lib/codemirror.css' rel='stylesheet' type='text/css'>
                <link href='http://ohai.ca/scripts/codemirror/theme/monokai.css' rel='stylesheet' type='text/css'>
                <script src='http://ohai.ca/scripts/codemirror/lib/codemirror.js'></script>
                <script src="http://ohai.ca/scripts/codemirror/mode/xml/xml.js"></script>
                <script src="http://ohai.ca/scripts/codemirror/mode/javascript/javascript.js"></script>
                <script src="http://ohai.ca/scripts/codemirror/mode/css/css.js"></script>
                <script src="http://ohai.ca/scripts/codemirror/mode/clike/clike.js"></script>
                <script src='http://ohai.ca/scripts/codemirror/mode/php/php.js'></script>
                <style type="text/css">
                    .tree {
                        background-color: #454d50; }
                        .tree * {
                            color: #eee; }
                    html, html * {
                        font-family:'Open Sans', 'Segoe UI', Arial, sans-serif;
                        font-size: 12px; }
                    body {
                        margin:0;
                        padding:0; }
                    a {
                        text-decoration:none; }
                        a:hover {
                            text-decoration:underline; }
                        a img {
                            border:0; }
                    .header {
                        margin:0 0 5px 0;
                        padding:5px;
                        font-size: 14pt;
                        color: <?php echo (HIGHLIGHT); ?>;
                        vertical-align:top; }
                    .small {
                        font-size: 10px; }
                    html .CodeMirror, html .CodeMirror * {
                        font-family: 'Droid Sans Mono', monaco, consolas, monospace;
                        font-size: 10pt;
                    }
                    .CodeMirror {
                        width: 100%;
                        height: 100%;
                    }
                </style>
                <script type='text/javascript'>
                    // http://blog.fedecarg.com/2011/07/12/javascript-asynchronous-script-loading-and-lazy-loading/
                    var loader=function(a,b){b = b||function(){};for(var c=a.length,d=c,e=function(){
                        if(!(this.readyState&&this.readyState!=="complete"&&this.readyState!=="loaded")){
                        this.onload=this.onreadystatechange=null;--d||b()}},f=document.getElementsByTagName("head")[0],
                        g=function(a){var b=document.createElement("script");b.async=true;
                        b.src=a;b.onload=b.onreadystatechange=e;f.appendChild(b)};c;)g(a[--c])};

                    var populate_tree = null;
                    var populate_tree_ex = null;
                    var my_save = null;
                    var parent_path = null;

                    loader (['http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js',
                             'http://ohai.ca/scripts/edit_area/edit_area_full.js'], function () {
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
                            my_save = function (id) {
                                $.ajax({
                                    type: 'POST',
                                    url: '<?php echo (basename (__FILE__)); ?>',
                                    data: {
                                        mode: '9',
                                        file: '<?php echo ($f); ?>',
                                        cwd:  '<?php echo ($c); ?>',
                                        p: editAreaLoader.getValue(id)
                                    },
                                    success: function (data) {
                                        alert (data);
                                    },
                                    error: function (data) {
                                        $('#save').click(); // non-ajax
                                    },
                                    dataType: 'html'
                                });
                            };

                            // global
                            populate_tree_ex = function (id, path) {
                                $.getJSON ('<?php echo basename (__FILE__); ?>', {
                                        'mode': '10',
                                        'cwd': path
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
                                var path_style = 'border-left: 3px <?php echo HIGHLIGHT; ?> solid; font-weight: bold;';
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

                            if ($('#p').length >= 1) {
                                /* editAreaLoader.init({
                                    id: "p" // id of the textarea to transform
                                    ,start_highlight: true  // if start with highlight
                                    ,allow_toggle: false
                                    ,word_wrap: false
                                    ,syntax: "php"
                                    ,replace_tab_by_spaces:4
                                    ,toolbar: "save,undo,redo,search,reset_highlight,word_wrap,fullscreen,select_font,syntax_selection"
                                    ,font_family: "'Droid Sans Mono', monaco, consolas, monospace"
                                    ,font_size: "9"
                                    ,save_callback: "my_save"
                                }); */
                                var editor = CodeMirror.fromTextArea(document.getElementById("p"), {
                                    lineNumbers: true,
                                    theme: "monokai",
                                    mode: "application/x-httpd-php",
                                    indentUnit: 4,
                                    smartIndent: true,
                                    tabSize: 4,
                                    indentWithTabs: false,
                                    matchBrackets: true,
                                    pollInterval: 200,
                                    undoDepth: 999,
                                    value: $('#p').val()
                                });
                            }

                            if ($('#filetree').length >= 1) {
                                populate_tree_ex ('filetree', '<?php echo $cwd; ?>');
                            }
                        });
                    });
                </script>
            </head>
<?php
    }
    switch ($m) { case 0:
        // frame
?>
        <frameset cols="300px,*">
            <frame name="tree" <?php echo ('src="?mode=1"'); ?> />
            <frame name="editor" <?php echo ('src="?mode=2"'); ?> />
        </frameset><noframes></noframes>
<?php
    break; case 1:
        // tree
        $dts=disk_total_space(getcwd());
        $dpf=($dts!=0)?round(disk_free_space(getcwd())/$dts*100,2):0; //calculate disk space
        $phv=phpversion();

        echo("<body class='tree'>
                <p id='filetree_head' class='header'></p>
                <form method='post' target='tree' action='?mode=5'>
                <!-- ?mode=5 is needed -->
                    <table id='filetree' cellspacing='0' cellpadding='2'></table>
                    <p class='header'>Selected items</p>
                    <label>
                        <input type='radio' name='act' value='rm'>
                        Delete
                    </label><br />
                    <label>
                        <input type='radio' name='act' value='archive'>
                        Archive
                    </label><br /><br />
                    <input type='hidden' name='cwd' value='$c' />
                    <input type='hidden' name='mode' value='5' />
                    <input type='hidden' name='fcount' value='$i' />
                    <input type='submit' />
                </form>
                <form method='post' target='tree' action='?mode=7'
                      enctype='multipart/form-data'>
                <!-- ?mode=7 is needed -->
                    <p class='header'>Upload</p>
                    <table>
                        <tr><td>File:</td>
                            <td>
                                <input type='file' name='fileobj' />
                            </td>
                        </tr>
                        <tr><td>Overwrite?</td>
                            <td>
                                <input type='checkbox' id='overwrite' name='overwrite' value='1'/>
                            </td>
                        </tr>
                        <tr><td></td>
                            <td>
                                <input type='hidden' name='mode' value='7' />
                                <input type='hidden' name='cwd' value='$c' />
                                <input type='submit' />
                            </td>
                        </tr>
                    </table>
                </form>
                <form method='post' target='tree' action='?mode=4'>
                <!-- ?mode=4 is needed -->
                    <p class='header'>Execute</p>
                    <table>
                        <tr><td>Command:</td>
                            <td>
                                <input type='text' name='act' />
                            </td>
                        </tr>
                        <tr><td>Param 1:</td>
                            <td>
                                <input type='text' id='p1' name='p1' value='$c'/>
                            </td>
                        </tr>
                        <tr><td>Param 2:</td>
                            <td>
                                <input type='text' id='p2' name='p2' />
                            </td>
                        </tr>
                        <tr><td></td>
                            <td>
                                <input type='hidden' name='mode' value='4' />
                                <input type='hidden' name='cwd' value='$c' />
                                <input type='submit' />
                            </td>
                        </tr>
                    </table>
                </form>
                <p>Commands: chmod(p1,p2), cp(p1,p2), delete(p1), exec(p1),
                    mkdir(p1), mkfile(p1), mv(p1,p2),
                    rename(p1,p2), rmdir(p1), touch(p1)</p>
                <hr />
                <p> PHP File Browser by Brian Lai.
                    <a href='https://github.com/1337/php-file-browser'>Get a copy!</a>
                </p>
            </body>
        </html>");
?>
<?php
    break; case 2:
        // editor
        if ($f) { // if I need to open/save a file then show...
            if (isset($_POST['p'])) { // save?
                $p=$_POST['p'];

                $pr = false;
                //pretend this is a backup
                if (BACKUP_BEFORE_SAVING) {
                    $pcd = date('ymd');
                    if (!file_exists ("$c/$f.b$pcd.bak")) { // copy only if not exists (saves first file of date)
                        $pr = @copy ("$c/$f","$c/$f.b$pcd.bak");
                    }
                    @chmod ("$c/$f.b$pcd.bak", fileperms (__FILE__)); // inherit file permissions
                }

                if ($pr == BACKUP_BEFORE_SAVING) {
                    $fh = @fopen("$c/$f", 'w') or die();
                    @fwrite ($fh, stripslashes($p));
                    fclose ($fh);
                    echo("<p>$c/<b>$f</b> is supposedly saved. (?)</p>");
                } else {
                    echo("<p><b>$f</b>
                    is <span style='color:red'>NOT</b> saved.</p>");
                }
            }

            $fh = @fopen ("$c/$f", 'r') or die('Failed to read file.');
            $p = @fread ($fh, @filesize("$c/$f")); //the @ is required because fread complains about a 0-len read
            fclose ($fh);

            echo("  <body style='overflow: hidden;'>
                        <form method='post'>
                            <textarea class='php editor'
                                       name='p'
                                         id='p'
                                      style='width:100%;height:100%;'>" .
                                htmlspecialchars ($p) . "</textarea><input type='hidden' name='cwd' value='$c' />
                            <input type='hidden' name='file' value='$f' />
                            <input type='hidden' name='mode' value='2' />
                            <input type='submit' name='save' id='save' value='Save' style='display:none' />
                        </form>
                    </body>
                </html>");
        }
?>
<?php
    break; case 3:
        // download - will fail if server RAM limit < filesize
        header ("Content-type: application/force-download");
        header ("Content-Disposition: attachment; filename=\"$f\"");
        // header ("Content-Length: " . @filesize("$c/$f"));
        @readfile ("$c/$f");
        exit();
?>
<?php
    break; case 4:
        // commands

        // transform params
        $p1 = htmlspecialchars(urldecode ($p1));
        $p2 = htmlspecialchars(urldecode ($p2));

        switch (strtolower ($a)) {
            case 'mv'; case 'rename':
                rename ($p1, $p2);
                break;
            case 'chmod':
                chmod ($p1, $p2);
                break;
            case 'cp':
                copy ($p1, $p2);
                break;
            case 'exec':
                exec ($p1);
                break;
            case 'mkdir':
                mkdir ($p1);
                break;
            case 'mkfile'; case 'touch':
                touch ($p1);
                break;
            case 'delete'; case 'rm':
                unlink ($p1);
                break;
            case 'rmdir':
                rmdir ($p1);
                break;
            default:
                die("No such command: $act");
                exit();
        }

        $cf = basename ($_SERVER['SCRIPT_FILENAME']);
        $pf = 'http://' . $_SERVER['SERVER_NAME'];

        header ("location: $pf/$cf?cwd=$c&file=$f&mode=1");
?>
<?php
    break; case 5:
        // group actions

        $ub = $_POST['fcount']; //upper bound of files in pane
        if (!$ub) die(); // do not proceed if you don't have anything to do

        for ($i=1; $i<=$ub; $i++) {
            $p1 = $_POST["c$i"]; // these are not the same params as mode 4
            $p2 = $_POST["f$i"];

            if ($p1 == '1') { // checkbox for this file is enabled
                switch (strtolower ($a)) {
                    case 'delete'; case 'rm'; case 'rmdir':
                        unlink ($p2);
                        break;
                    case 'archive':
                        $pcd = date('ymd');
                        mkdir ("$c/archive.b$pcd/");
                        rename ($p2, "$c/archive.b$pcd/" . basename ($p2));
                    default:
                }
            }
        }

        $cf = basename ($_SERVER['SCRIPT_FILENAME']);
        $pf = 'http://' . $_SERVER['SERVER_NAME'];

        header ("location: $pf/$cf?cwd=$c&file=$f&mode=1");
?>
<?php
    break; case 6:
        // current folder, download only
        $cwd = getcwd ();
        echo("<body>
                <p class='header'>
                    <b>" . basename ($c) . "</b>
                </p>
                <table cellspacing='0'
                       cellpadding='2'
                       style='display:block;margin:auto;width:500px;'>");

        $da = filelist ($c,1);
        $i = 0;
        foreach ($da as $pn) {
            if (substr($pn,0,1) != '.') { // don't show hidden files
                $i++;

                printf ("    <tr %s>
                                <td style='width:100%%;'>
                                    <a href='?file=$pn&amp;mode=3' target='_blank'>$pn</a>
                                </td>
                            </tr>",
                        (($i % 2 == 0)? "style='background-color:#eee;'": ''));
            }
        }
        echo("      </table>
                </body>
            </html>");
?>
<?php
    break; case 7:
        // current folder, upload only
        // this provides no feedback, and overwrites any files.
        if (isset ($_FILES['fileobj'])) {
            if (isset ($_POST['overwrite']) && $_POST['overwrite'] == '1') {
                // upload if file exists (too).
                move_uploaded_file ($_FILES['fileobj']['tmp_name'],
                            "$c/" . $_FILES['fileobj']['name']);
            } else {
                // upload if file doesn't exist.
                if (!file_exists ("$c/" . $_FILES['fileobj']['name'])) {
                    move_uploaded_file ($_FILES['fileobj']['tmp_name'],
                                "$c/" . $_FILES['fileobj']['name']);
                }
            }
        }
        $cf = basename ($_SERVER['SCRIPT_FILENAME']);
        $pf = 'http://' . $_SERVER['SERVER_NAME'];
        header ("location: $pf/$cf?cwd=$c&file=$f&mode=1");
?>
<?php
    break; case 8:
        // login window to set login cookies
        // if no cookie is set, all modes will redirect here.
        echo ("<html>
                    <head><style type='text/css'>
                        input {border: 1px solid silver;padding:5px;}
                    </style></head>
                    <body style='background-color:#eee;font-family:sans-serif;
                                 line-height:1.5em;font-size:0.8em;'>
                        <div style='background-color:#fff;position:fixed;
                                    left:50%;top:50%;width:250px;margin-left:-125px;
                                    height:150px;margin-top:-75px;text-align:center;
                                    padding:20px;border:1px solid silver;'>
                            <form method='post'>
                                <label for='username'>User name: </label><br />
                                <input id='username' name='username' type='text' /><br />
                                <label for='password'>Password: </label><br />
                                <input id='password' name='password' type='password' /><br />
                                <br />
                                <input type='submit' value='Log in' />
                            </form>
                        </div>
                    </body>
                </html>");
?>
<?php
    break; case 9:
        // ajax file upload
        if ($f) { // if I need to open/save a file then show...
            if (isset($_POST['p'])) { // save?
                $p=$_POST['p'];

                $pr = false;
                //pretend this is a backup
                if (BACKUP_BEFORE_SAVING) {
                    $pcd = date('ymd');
                    if (!file_exists ("$c/$f.b$pcd.bak")) { // copy only if not exists (saves first file of date)
                        $pr = @copy ("$c/$f","$c/$f.b$pcd.bak");
                    }
                    @chmod ("$c/$f.b$pcd.bak", fileperms (__FILE__)); // inherit file permissions
                }

                if ($pr == BACKUP_BEFORE_SAVING) {
                    $fh = @fopen("$c/$f", 'w') or die();
                    @fwrite ($fh, stripslashes($p));
                    fclose ($fh);
                    echo ("Saved.");
                } else {
                    echo ("Failed to save $cwd/$f !");
                }
            }
        }
?>
<?php
    break; case 10:
        // return JSON file list of a given folder.

        switch ($p1) { // parameter 1 (p1); optional
            case '0': // dirs
                $da = filelist ($c, 0);
                break;
            case '1': // files
                $da = filelist ($c, 1);
                break;
            case '2': // everything
            default:
                $da = array_merge (filelist ($c, 0),filelist ($c, 1));
                break;
        }
        $output = array ();
        foreach ($da as $pn) {
            $ft = is_dir ("$c/$pn") ? "dir" : "file";
            $fs = is_file ("$c/$pn") ? filesize_natural (@filesize ("$c/$pn")) : "";
            try {
                $fp = fileperm ("$c/$pn");
            } catch (Exception $e) {
                $fp = '0000';
            }
            $output[] = array (
                'name' => $pn,
                'path' => $cwd,
                'type' => $ft,
                'size' => $fs,
                'perm' => $fp,
            );
        }
        header ("Content-type: application/json");
        echo (json_encode ($output));
?>
<?php
    break; case 99:
        // debug
        echo ("<pre>");
        print_r ($_GET);
        print_r ($_POST);
        echo ("</pre>");
?>
<?php
    break; default:
    }
?>
