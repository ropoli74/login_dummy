<?php
require_once(dirname(__FILE__).'/classes/User.php');

if (User::is_logged_in())
{
    $user = User::factory_from_session();

    echo 'HELLO <b>'.strtoupper($user->get_firstname()).'</b> WELCOME TO SECURED SITE<br><br>';
    echo 'Here is your Userdata:';
    p($user);
    echo '<a href="logout.php">logout</a>';
}
else
{
    echo 'ACCESS DENIED<br>';
    echo '<a href="login.php">login</a>';
}

function p($var)
{
    echo '<pre>';
    print_r($var);
    echo '<pre>';
}