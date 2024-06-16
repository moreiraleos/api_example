<?php
require_once("./vendor/autoload.php");

use Classes\Database;
use Classes\Jwthandler;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Method: POST");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


// Data from on request
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

var_dump($_SERVER["REQUEST_METHOD"]);

// CHECK SE O METODO É DIFERENTE A POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = msg(0, 404, "Page Not Fout");
} elseif (
    !isset($data->email)
    ||  !isset($data->password)
    || empty($data->email)
    || empty($data->password)
) {
    $fields = ["fields" => ["email", "password"]];
    $returnData = msg(0, 422, "Please Fill in all required filed", $fields);
} else {
    $email = trim($data->email);
    $password = trim($data->password);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid Email Address');
    } elseif (strlen($password) < 8) {
        $returnData = msg(0, 422, "Your password must be at least 8 characters loog!");
    } else {
        try {
            $fetch_user_by_email = "SELECT * FROM users WHERE email = :email";
            $query_stmt = Database::connect()->prepare($fetch_user_by_email);
            $query_stmt->bindValue(":email", $email);
            $query_stmt->execute();

            if ($query_stmt->rowCount()) {
                $row = $query_stmt->fetch(PDO::FETCH_OBJ);
                $check_password = password_verify($password, $row->password);

                if ($check_password) {
                    $jwt = new Jwthandler();
                    $token = $jwt->jwtEncodeData(
                        "http://localhost/curso_novo/",
                        [
                            "user_id" => $row->id
                        ]
                    );

                    $returnData = [
                        "success" => 1,
                        "message" => "You have successfully logged in",
                        "token" => $token
                    ];
                } else {
                    $returnData = msg(0, 422, "invalid Password");
                }
            } else {
                $returnData = msg(0, 422, "invalid Email Address");
            }
        } catch (\Throwable $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}


echo json_encode($returnData);
###################
function msg($success, $status, $message, $extra = [])
{
    return array([
        "success" => $success,
        "status" => $status,
        "message" => $message
    ], $extra);
}
