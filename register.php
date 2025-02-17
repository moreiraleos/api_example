<?php
require_once("./vendor/autoload.php");

use Classes\Database;


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Method: POST");
header("Content-type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Headers: Content-Type, 
// Access-Control-Allow-Headers,Authorization, X-Requested-With");

header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


// Data from on request
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $returnData = msg(0, 404, 'Page Not Found!');
} elseif (
    !isset($data->name)
    || !isset($data->email)
    || !isset($data->password)
    || empty(trim($data->name))
    || empty(trim($data->email))
    || empty(trim($data->password))
) {
    $fields = ['fields' => ['name', 'email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);
} else {
    $name = trim($data->name);
    $email = trim($data->email);
    $password = trim($data->password);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid Email Address!');
    } elseif (strlen($password) < 8) {
        $returnData = msg(0, 422, 'Your password must be at least characters long!');
    } elseif (strlen($name) < 3) {
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');
    } else {
        try {
            $check_email = "SELECT email FROM users WHERE email = :email";
            $check_email_stmt = Database::connect()->prepare($check_email);
            $check_email_stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $check_email_stmt->execute();
            if ($check_email_stmt->rowCount()) {
                $returnData = msg(0, 422, 'This e-mail already in user!');
            } else {
                $insert_query = "INSERT INTO users(name, email, password) 
                VALUES(:name, :email, :password)";
                $insert_stmt = Database::connect()->prepare($insert_query);
                $insert_stmt->bindValue(":name", htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
                $insert_stmt->bindValue("email", $email, PDO::PARAM_STR);
                $insert_stmt->bindValue(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);

                $insert_stmt->execute();
                $returnData = msg(1, 201, 'You have a successfully registered');
            }
        } catch (PDOException $e) {
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
