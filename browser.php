<?php
    /*  PHP File Browser (GPLv3) 2012 Brian Lai
        Code IDE with built-in version tracking

        Do not edit this script with itself.
    */

    // settings ===============================================================
    error_reporting(E_ALL);

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
    define ('CDN_ROOT', 'https://raw.github.com/1337/php-file-browser/master/');
    define ('FRAME', 0);
    define ('TREE', 1);
    define ('EDITOR', 2);
    define ('DOWNLOAD', 3);
    define ('COMMAND_LINE', 4);
    define ('GROUP_ACTIONS', 5);
    define ('DOWNLOAD_HERE', 6);
    define ('UPLOAD_HERE', 7);
    define ('LOGIN', 8);
    define ('AJAX_FILE_SAVE', 9);
    define ('JSON_TREE', 10);
    define ('DEBUG', 99);

    define ('DIRS_ONLY', 0);
    define ('FILES_ONLY', 1);
    define ('DIRS_AND_FILES', 2);

    $config = array (
        'VERSION' => 5.1,
        'HOSTNAME' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
        'BACKUP_BEFORE_SAVING' => true,
        'SHOW_HIDDEN_OBJECTS' => true, // unix only!
        'SHOW_BACKUP_OBJECTS' => false, // show .b??????.bak files
        'CHECK_PASSWORD' => false, // show login window
        'ALLOWED_USERS' => array (
            // user => sha1 hash of the password
            'brian' => '526242588032599f491f36c10137c88c076384ef',
            'guest' => '787373e81b9e76715abeae529faf9a0a9dbf5079'
        )
    );


    // classes / functions ====================================================
    class DirTools {
        public $directory; // used by directory operations

        function __construct($directory) {
            $this->directory = str_replace("\\", '/', $directory);
            if (substr($this->directory, -1) !== '/') {
                $this->directory .= '/'; // auto-add trailing slash
            }
        }

        function __toString() {
            return $this->directory;
        }

        private static function _filter_backup_objects($name) {
            // FALSE are removed
            return substr($name, -4, 4) !== '.bak';
        }

        private static function _filter_hidden_objects($name) {
            // FALSE are removed
            return $name[0] !== '.';
        }

        public function files($what = DIRS_AND_FILES) {
            /*  returns all files from $directory.

                what 0 = dirs only
                     1 = files only
                     2 = everything
            */
            global $config;
            $file_objects = array ();

            switch ($what) {
                case DIRS_ONLY:
                    $files = glob($this->directory . '/*', GLOB_ONLYDIR);
                    break;
                case FILES_ONLY:
                    $files = array_diff(
                        glob($this->directory . '/*'),
                        glob($this->directory . '/*', GLOB_ONLYDIR)
                    );
                    break;
                case DIRS_AND_FILES:
                default:
                    $files = glob($this->directory . '/*');
                    break;
            }
            if (sizeof($files) > 0) {
                sort($files); // sort will explode if count = 0
                if ($config['SHOW_BACKUP_OBJECTS'] !== true) {
                    $files = array_filter(
                        $files,
                        array ($this, '_filter_backup_objects')
                    );
                }
                if ($config['SHOW_HIDDEN_OBJECTS'] !== true) {
                    $files = array_filter($files, array (
                        $this, '_filter_hidden_objects'));
                }
            }
            foreach ($files as &$file) {
                $file = str_replace("\\", '/', $file);
                $file_objects[] = new FileTools (
                    $this->directory,
                    basename($file)
                );
            }
            return $file_objects;
        }

        public function perms() {
            try {
                return substr(sprintf('%o', fileperms($this->directory)), -4);
            } catch (Exception $e) {
                return '0000';
            }
        }
    }


    class FileTools {
        public $directory; // used by directory operations
        public $filename; // used by single-file operations

        function __construct($directory, $filename) {
            $this->directory = str_replace("\\", '/', $directory);
            if (substr($this->directory, -1) !== '/') {
                $this->directory .= '/'; // auto-add trailing slash
            }
            $this->filename = str_replace("\\", '/', $filename);
        }

        function __toString() {
            return $this->directory . $this->filename;
        }

        public function extension() {
            return pathinfo($this->filename, PATHINFO_EXTENSION);
        }

        public function perms() {
            try {
                return substr(sprintf('%o', fileperms($this->filename)), -4);
            } catch (Exception $e) {
                return '0000';
            }
        }

        public function natural_size() {
            // Modded snippet from PHP Share: http://www.phpshare.org
            $bytes = filesize($this->filename);
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } else if ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } else if ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } else {
                $bytes = $bytes . ' B';
            }
            return $bytes;
        }

        public function backup_file() {
            $pcd = date('ymd');
            return $this->directory . $this->filename . '.b' . $pcd . '.bak';
        }

        public function is_hidden() {
            return $this->filename[0] === '.';
        }
    }


    function vars($index = false, $default = null) {
        // gathers everything from the request.
        // see cached version of this function: github.com/1337/pop
        @session_start();
        $vars = (array)array_merge(
            (array)$_COOKIE,
            (array)$_SESSION,
            (array)$_POST,
            (array)$_GET
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


    // public subs ============================================================
    $act = vars('act');
    $cwd = new DirTools (vars('cwd', getcwd()));
    $mode = vars('mode', 0); // 0 = frame page
    $file = new FileTools ($cwd, vars('file'));
    $file_base = basename($file);
    $param1 = vars('param1'); // params for $act
    $param2 = vars('param2');
    $username = vars('username');
    $password = vars('password');

    chdir($cwd); // because

    if ($config['CHECK_PASSWORD'] === true && strlen($username) > 0) {
        // login request
        if (array_key_exists($username, $config['ALLOWED_USERS']) &&
            (sha1($password) === $config['ALLOWED_USERS'][$username])
        ) {
            setcookie("username", $username, time() + 36000);
            setcookie("password", $password, time() + 36000);
        } else {
            $mode = LOGIN; // authentication failed; redirect to login page
        }
    }

    // For modes with html heads, print head now.
    if (in_array($mode, array (FRAME, TREE, EDITOR, DOWNLOAD_HERE, LOGIN))) {
        $css_files = array (
            'http://fonts.googleapis.com/css?family=Open+Sans',
            'http://fonts.googleapis.com/css?family=Droid+Sans+Mono',
            CDN_ROOT . 'scripts/codemirror/lib/codemirror.css',
            CDN_ROOT . 'scripts/codemirror/theme/monokai.css',
            CDN_ROOT . 'css/custom.css'
        );
        ?>
        <html><head>
        <?php foreach ($css_files as $css) {
        echo "<link rel='stylesheet' type='text/css' href='$css' />";
    } ?>
        <script type="text/javascript"
                src='https://raw.github.com/1337/Lazyload/master/lazyload.min.js'></script>
        <script type="text/javascript"
                src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'></script>
        <script type='text/javascript'>
            var populate_tree = null;
            var populate_tree_ex = null;
            var parent_path = null;
        </script>
    </head>
        <?php
    }

    // split page code ========================================================
    switch ($mode) {
        case FRAME:
            ?>
        <frameset cols="300px,*">
            <frame name="tree" src="?mode=1"/>
            <frame name="editor" src="?mode=2"/>
        </frameset>
        <noframes></noframes>
            <?php
            break;
        case TREE:
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
            <?php
            break;
        case EDITOR:
            // if I need to open/save a file then show...
            if (!strlen($file) || !is_file($file)) {
                die ("To begin, click on a file name in the file panel.");
            }
            $content = file_get_contents($file);
            ?>
        <body style='overflow: hidden;'>
            <form method='post'>
                <textarea class='php editor' style='width:100%;height:100%;'
                          name='content'
                          id='content'><?php echo htmlspecialchars($content); ?></textarea>
                <input type='hidden' name='cwd' value='<?php echo $cwd; ?>'/>
                <input type='hidden' name='file'
                       value='<?php echo $file_base; ?>'/>
                <input type='hidden' name='mode' value='2'/>
                <input type='submit' name='save' id='save'
                       value='Save' style='display:none'/>
            </form>
        <script type="text/javascript"
                src="<?php echo CDN_ROOT; ?>scripts/codemirror/lib/codemirror.js"></script>
        <script type="text/javascript">
            $L(['<?php echo CDN_ROOT; ?>scripts/codemirror/mode/xml/xml.js',
                '<?php echo CDN_ROOT; ?>scripts/codemirror/mode/javascript/javascript.js',
                '<?php echo CDN_ROOT; ?>scripts/codemirror/mode/css/css.js',
                '<?php echo CDN_ROOT; ?>scripts/codemirror/mode/clike/clike.js',
                '<?php echo CDN_ROOT; ?>scripts/codemirror/mode/php/php.js'], function () {
                $(document).ready(function () {
                    var editor = CodeMirror.fromTextArea(document.getElementById("content"), {
                        lineNumbers:true,
                        theme:"monokai",
                        mode:"application/x-httpd-php",
                        indentUnit:4,
                        smartIndent:true,
                        tabSize:4,
                        indentWithTabs:false,
                        matchBrackets:true,
                        pollInterval:200,
                        undoDepth:999,
                        value:$('#content').val()
                    });
                });
            });
        </script>
            <?php
            break;
        case DOWNLOAD: // will fail if server RAM limit < filesize
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=\"$file_base\"");
            readfile($file);
            exit();
            ?><?php
            break;
        case COMMAND_LINE:

            // transform params
            $param1 = htmlspecialchars(urldecode($param1));
            $param2 = htmlspecialchars(urldecode($param2));

            switch (strtolower($act)) {
                case 'mv':
                    rename($param1, $param2);
                    break;
                case 'chmod':
                    chmod($param1, $param2);
                    break;
                case 'cp':
                    copy($param1, $param2);
                    break;
                case 'mkdir':
                    mkdir($param1);
                    break;
                case 'touch':
                    touch($param1);
                    break;
                case 'rm':
                    unlink($param1);
                    break;
                case 'rmdir':
                    rmdir($param1);
                    break;
                default:
                    die("No such command: $act");
            }

            $cf = basename($_SERVER['SCRIPT_FILENAME']);
            $pf = 'http://' . $_SERVER['SERVER_NAME'];
            header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=1");
            ?><?php
            break;
        case GROUP_ACTIONS:
            $i = 0;
            do {
                $i++;
                $param1 = vars('c' . $i); // these are not the same params as mode 4
                $param2 = vars('f' . $i);

                if ($param1 === '1') { // checkbox for this file is enabled
                    switch (strtolower($a)) {
                        case 'rm';
                        case 'rmdir':
                            unlink($param2);
                            break;
                        case 'archive':
                            $pcd = date('ymd');
                            mkdir("$cwd/archive.b$pcd/");
                            rename($param2, "$cwd/archive.b$pcd/" . basename($param2));
                        default:
                    }
                }
            } while (vars('c' . $i));

            $cf = basename($_SERVER['SCRIPT_FILENAME']);
            $pf = 'http://' . $_SERVER['SERVER_NAME'];

            header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=1");
            ?><?php
            break;
        case DOWNLOAD_HERE: // current folder, download only
            $files = $cwd->files(FILES_ONLY);
            ?>
        <body>
        <p class='header'>
            <b><?php echo basename((string)$cwd); ?></b>
        </p>
        <table class='filetree' cellspacing='0' cellpadding='2'>");
            <?php foreach ($files as $idx => $file) {
                if (!$pn->is_hidden()) { // don't show hidden files
                    echo "
                    <tr><td style='width:100%%;'>
                        <a href='?file=$file&amp;mode=",
                    DOWNLOAD,
                    "' target='_blank'>$file</a>
                    </td></tr>
                        ";
                }
            } ?>
        </table>
        </body>
        </html>
<?php
            break;
        case UPLOAD_HERE:
            // this provides no feedback, and overwrites any files.
            if (isset ($_FILES['fileobj'])) {
                if (isset ($_POST['overwrite']) && $_POST['overwrite'] == '1') {
                    // upload if file exists (too).
                    move_uploaded_file($_FILES['fileobj']['tmp_name'],
                                       "$cwd/" . $_FILES['fileobj']['name']);
                } else {
                    // upload if file doesn't exist.
                    if (!file_exists("$cwd/" . $_FILES['fileobj']['name'])) {
                        move_uploaded_file($_FILES['fileobj']['tmp_name'],
                                           "$cwd/" . $_FILES['fileobj']['name']);
                    }
                }
            }
            $cf = basename($_SERVER['SCRIPT_FILENAME']);
            $pf = 'http://' . $_SERVER['SERVER_NAME'];
            header("location: $pf/$cf?cwd=$cwd&file=$file_base&mode=1");
            ?><?php
            break;
        case LOGIN:
            // login window to set login cookies
            // if no cookie is set, all modes will redirect here.
            ?>
        <html>
            <head>
                <style type='text/css'>
                    input {
                        border: 1px solid silver;
                        padding: 5px;
                    }
                </style>
            </head>
            <body style='background-color:#eee;font-family:sans-serif;
                         line-height:1.5em;font-size:0.8em;'>
                <div style='background-color:#fff;position:fixed;
                            left:50%;top:50%;width:250px;margin-left:-125px;
                            height:150px;margin-top:-75px;text-align:center;
                            padding:20px;border:1px solid silver;'>
                    <form method='post'>
                        <label for='username'>User name: </label><br/>
                        <input id='username' name='username' type='text'/>
                        <br/>
                        <label for='password'>Password: </label><br/>
                        <input id='password' name='password' type='password'/>
                        <br/>
                        <br/>
                        <input type='submit' value='Log in'/>
                    </form>
                </div>
            <?php
            break;
        case AJAX_FILE_SAVE:
            // if I need to open/save a file then show...
            if (!strlen($file) || !is_file($file)) {
                if (vars('content')) { // save?
                    $content = vars('content');

                    //pretend this is a backup
                    if (BACKUP_BEFORE_SAVING) {
                        if (!file_exists($file->backup_file())) {
                            // copy only if not exists (saves first file of date)
                            copy($file, $file->backup_file());
                        }
                        // inherit file permissions
                        chmod($file->backup_file(), fileperms(__FILE__));
                    }

                    if (file_get_contents($file, $content) === false) {
                        echo ("Failed to save $file !");
                    } else {
                        echo ("Saved.");
                    }
                }
            }
            ?><?php
            break;
        case JSON_TREE:
            // return JSON file list of a given folder.
            $files = $cwd->files((int)$param1); // defaults to show everything
            $output = array ();
            foreach ($files as $file) {
                if (is_file($file)) {
                    $file_object = new FileTools ($cwd, $file);
                    $output[] = array (
                        'name' => basename($file_object),
                        'path' => (string)$cwd,
                        'type' => 'file',
                        'size' => $file_object->natural_size(),
                        'perm' => $file_object->perms()
                    );
                } else if (is_dir($file)) {
                    $dir_object = new DirTools ($file);
                    $output[] = array (
                        'name' => basename($dir_object),
                        'path' => (string)$cwd,
                        'type' => 'dir',
                        'size' => '',
                        'perm' => $dir_object->perms()
                    );
                }
            }
            // header ("Content-type: application/json");
            echo (json_encode($output));
            ?><?php
            break;
        case DEBUG:
            // what do you want to debug?
            break;
        default:
    }

    if (in_array($mode, array (FRAME, TREE, EDITOR, DOWNLOAD_HERE, LOGIN))) {
        // For modes with html heads, print tail now.
        ?>
            </body>
        </html>
<?php
    }

// prevent printing EOF space