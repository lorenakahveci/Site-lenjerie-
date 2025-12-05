<?php
session_start();
require_once "../includes/db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');
$password = $data['pass'] ?? ($data['password'] ?? '');
$response = ["success"=>false,"msg"=>"Email sau parolă incorectă"];

if(!$email || !$password){
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s",$email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if($user && password_verify($password,$user['password'])){
    $_SESSION['user'] = [
        "id" => $user['id'],
        "username" => $user['username'],
        "email" => $user['email']
    ];
    $_SESSION['user_id'] = $user['id'];
    $response = ["success"=>true,"msg"=>"Autentificare reușită!"];
}
echo json_encode($response);
?>
