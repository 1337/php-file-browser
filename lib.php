<?php
    /**
     * public classes / functions
     */

    if (!defined('PROJECT_ROOT')) {
        die();
    }

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

        public function files($what=2 /* DIRS_AND_FILES */) {
            /*  returns all files from $directory.

                what 0 = dirs only
                     1 = files only
                     2 = everything
            */
            global $config;
            $file_objects = array ();

            switch ($what) {
                case 0 /* DIRS_ONLY */:
                    $files = glob($this->directory . '/*', GLOB_ONLYDIR);
                    break;
                case 1 /* FILES_ONLY */:
                    $files = array_diff(
                        glob($this->directory . '/*'),
                        glob($this->directory . '/*', GLOB_ONLYDIR)
                    );
                    break;
                case 2 /* DIRS_AND_FILES */:
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
                    $files = array_filter(
                        $files, array ($this, '_filter_hidden_objects'));
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

        public function icon() {
            return 'img/folder.png';
        }
    }


    class FileTools {
        public $directory,  // used by directory operations
                $filename;  // used by single-file operations

        function __construct($directory, $filename) {
            $this->directory = str_replace("\\", '/', $directory);
            if (substr($this->directory, -1) !== '/') {
                $this->directory .= '/'; // auto-add trailing slash
            }
            $this->filename = str_replace("\\", '/', $filename);

            if (!is_file($this->filename)) {
                return null;
            }
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

        public function size() {
            try {
                return filesize($this->filename);
            } catch (Exception $e) {
                return -1;
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

        public function backup() {
            // make a backup copy.
            // return will be (success).

            copy($this->__toString(), $this->backup_file());

            // inherit file permissions (of this script, not the file)
            chmod($this->backup_file(), fileperms(__FILE__));

            return (file_get_contents($this->backup_file()) !== false);
        }

        public function backup_file() {
            // get the name of a backup file if you were to make one now.
            $pcd = date('ymdHis');
            return $this->directory . $this->filename . '.b' . $pcd . '.bak';
        }

        public function revisions() {
            // returns the backup file names.
            return glob($this->filename . '.b*.bak');
        }

        public function is_hidden() {
            // implies this function works only on *nix
            return $this->filename[0] === '.';
        }

        public function icon() {
            switch ($this->extension()) {
                case 'jpg';
                case 'jpeg';
                case 'bmp';
                case 'gif';
                case 'png':
                    return 'img/image.png';
                case 'txt';
                case 'pdf';
                case 'doc';
                case 'docx':
                    return 'img/document.png';
                case 'htm':
                case 'html':
                case 'css':
                case 'php':
                case 'phpx':
                case 'js':
                case 'py':
                    return 'img/html.gif';
                default:
                    return 'img/default.png';
            }
        }

        public function codemirror_mode () {
            // guess the highlight mode.
            switch ($this->extension()) {
                case 'htm':
                case 'html':
                case 'php':
                case 'phpx':
                    return "application/x-httpd-php";
                case 'css':
                    return "css";
                case 'js':
                    return "javascript";
                case 'py':
                    return "text/x-python";
                default:
                    return "text/html";
            }
        }
    }

    function vars($index=false, $default=null) {
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

    function check_password($username, $password, $hash='') {
        global $SETTINGS;

        $salt = '';  // the password was not salted

        if (strlen($username) <= 0 || strlen($password) <= 0) {
            // improper request
            return false;
        }

        if (!array_key_exists($username, $SETTINGS['ALLOWED_USERS'])) {
            // user not registered
            return false;
        }

        if (strpos($hash, ';') !== false) {
            // that is, a separator was found in the hash
            $components = explode(';', $hash, 2);

            // the password was generated with $salt Appended
            $hash = $components[0];
            $salt = $components[1];
        }

        // do the actual check
        return strtolower(sha1($password . $salt)) === strtolower($hash);
    }

    function mode($which, $echo=true) {
        // shorthand helper for the mode.
        global $SETTINGS;

        $mode = $SETTINGS['MODES'][$which];
        if ($echo === true) {
            echo $mode;
        }
        return $mode;
    }