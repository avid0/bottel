<?php

require "vendor/autoload.php";

$admin = 209649501;
$token = '681948689:AAFqSBlyGXTsDOvCpgdd0FRnLI79-p5saOo';

use Bottel\Bottel;
use Bottel\Request;


$bot = new Bottel($token);
$statement = $bot->statement->sendMessage(
    chat_id: $admin,
    text: "neil",
);
$statement->text = "god is dead";
$statement->send();

?>