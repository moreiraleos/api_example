<?php
require_once("./vendor/autoload.php");

use App\AuthMiddleware;
use Classes\Database;
use Classes\Jwthandler;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Method: POST");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


$allHeaders = getallheaders();

$auth = new AuthMiddleware(Database::connect(), $allHeaders);

echo json_encode($auth->isValid());
