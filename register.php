<?php
session_start();
require_once "../includes/db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$pass = $data['pass'] ?? ($data['password'] ?? '');

if(!$username || !$email || !$pass){
    echo json_encode(["success"=>false,"msg"=>"Completează toate câmpurile"]);
    exit;
}

// verificăm email
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s",$email);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows>0){
    echo json_encode(["success"=>false,"msg"=>"Email deja folosit"]);
    exit;
}

// creare cont
$hash = password_hash($pass,PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users(username,email,password) VALUES(?,?,?)");
$stmt->bind_param("sss",$username,$email,$hash);
if($stmt->execute()){
    $id = $conn->insert_id;
    $_SESSION['user'] = ["id"=>$id,"username"=>$username,"email"=>$email];
    $_SESSION['user_id'] = $id;
    echo json_encode(["success"=>true,"msg"=>"Cont creat cu succes!"]);
    exit;
}
echo json_encode(["success"=>false,"msg"=>"Eroare la creare cont"]);
?>
