<?php
require_once(dirname(__FILE__).'/classes/User.php');

if (User::is_logged_in())
{
	header("Location: index.php");
}

if ( ! array_key_exists('username', $_POST)
	OR ! array_key_exists('password', $_POST))
{
	echo 'Please give username and password';
	exit;
}

$remember = array_key_exists('remember', $_POST) ? TRUE : FALSE;

$user = User::factory_from_session();
$logged_in = $user->login($_POST['username'], $_POST['password'], $remember);

if ($logged_in !== FALSE)
{
	header("Location: secured_site.php");
}
else
{
	echo 'ACCESS DENIED<br />';
    echo 'Please verify username or password or <a href="register.php">register</a>';
}