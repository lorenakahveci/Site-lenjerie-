<?php
require_once __DIR__ . "/db.php";

// DEBUG: verificam conexiunea
if (!isset($conn)) {
    die("AUTH ERROR: conn is not set");
}
if (!($conn instanceof mysqli)) {
    die("AUTH ERROR: conn is NOT a mysqli object. Current type: " . gettype($conn));
}
if ($conn->connect_error) {
    die("AUTH ERROR: mysqli connect error → " . $conn->connect_error);
}

// Returnează user
function getLoggedInUser() {
    if (!isset($_SESSION["user_id"])) { 
        return null;
    }

    global $conn;

    $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id=? LIMIT 1");

    if (!$stmt) {
        die("AUTH ERROR: prepare() failed in getLoggedInUser → " . $conn->error);
    }

    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc() ?: null;
}

// Logout
function logoutUser() {
    global $conn;

    if (!($conn instanceof mysqli)) {
        die("AUTH ERROR: logoutUser(): conn is not mysqli");
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("UPDATE users SET remember_token=NULL WHERE id=?");

        if (!$stmt) {
            die("AUTH ERROR: logoutUser(): prepare() failed → " . $conn->error);
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    session_unset();
    session_destroy();
    setcookie("remember_me", "", time() - 3600, "/");

    return true;
}
?>
