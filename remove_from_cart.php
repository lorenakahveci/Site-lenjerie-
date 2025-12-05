<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/auth.php";

$user = getLoggedInUser();
if(!$user){ echo json_encode(["success"=>false,"msg"=>"Trebuie să fii logat"]); exit; }

$raw = trim(file_get_contents("php://input"));
$data = json_decode($raw,true);
if(!is_array($data)) $data = $_POST ?? [];

$id = isset($data['id']) ? intval($data['id']) : 0;
if($id<=0){ echo json_encode(["success"=>false,"msg"=>"Parametru invalid"]); exit; }

$user_id = $user['id'];

$stmt=$conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii",$user_id,$id);
$stmt->execute();
$stmt->close();
echo json_encode(["success"=>true]);
exit;
