<?php
require_once(dirname(__FILE__).'/classes/User.php');

$user = User::factory_from_session();
$user->logout(TRUE);