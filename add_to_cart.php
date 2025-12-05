<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/auth.php";

function jsonErr($msg){
    echo json_encode(["success"=>false,"msg"=>$msg]);
    exit;
}

$user = getLoggedInUser();
if(!$user) jsonErr("Trebuie să fii logat");

$raw = trim(file_get_contents("php://input"));
$data = json_decode($raw,true);
if(!is_array($data)) $data = $_POST ?? [];

$id = isset($data['id']) ? intval($data['id']) : 0;
$quantity = isset($data['quantity']) ? intval($data['quantity']) : 1;
if($quantity<=0) $quantity=1;
if($id<=0) jsonErr("Produs invalid");

$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id=? LIMIT 1");
$stmt->bind_param("i",$id);
$stmt->execute();
$prod = $stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$prod) jsonErr("Produs inexistent");

$user_id = $user['id'];

$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND product_id=? LIMIT 1");
$stmt->bind_param("ii",$user_id,$id);
$stmt->execute();
$res = $stmt->get_result();

if($res && $res->num_rows>0){
    $stmt->close();
    $stmt2 = $conn->prepare("UPDATE cart SET quantity=quantity+? WHERE user_id=? AND product_id=?");
    $stmt2->bind_param("iii",$quantity,$user_id,$id);
    $stmt2->execute();
    $stmt2->close();
}else{
    $stmt->close();
    $stmt3 = $conn->prepare("INSERT INTO cart(user_id,product_id,quantity) VALUES(?,?,?)");
    $stmt3->bind_param("iii",$user_id,$id,$quantity);
    $stmt3->execute();
    $stmt3->close();
}

// returnează coș
$stmt = $conn->prepare("
    SELECT c.product_id AS id,c.quantity,p.name,p.price,p.image
    FROM cart c
    JOIN products p ON p.id=c.product_id
    WHERE c.user_id=?
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
$total = 0;
while($row=$result->fetch_assoc()){
    $items[] = $row;
    $total += $row['price']*$row['quantity'];
}
$stmt->close();

echo json_encode(["success"=>true,"cart"=>$items,"total"=>number_format($total,2)]);
exit;
