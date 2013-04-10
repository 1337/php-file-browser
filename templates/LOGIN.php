<?php
    /**
     * login page
     */
    if (!defined('PROJECT_ROOT')) {
        die();
    }
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
    </body>
</html>