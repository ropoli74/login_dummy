<?php
require_once(dirname(__FILE__).'/classes/User.php');


$user = new User();
$user->set_id(1);
$user->set_email('lang294@gmail.com');
$user->set_firstname('Roland');
$user->set_lastname('Lang');
$user->set_password(password_hash('29041974', PASSWORD_DEFAULT));
$user->save();

p('User registered');

$user->login('lang294@gmail.com', '29041974', TRUE);

p('User logged in');
p($user);

function p($var)
{
    echo '<pre>';
    print_r($var);
    echo '<pre>';
}
