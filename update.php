<?php
session_start();
 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
 
$servername = "localhost";
$username = "u52989";
$password = "5004286";
$dbname = "u52989";
 
// Ñîçäàíèå ïîäêëþ÷åíèÿ
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
$user_id = $_SESSION['user_id'];
 
// Îáðàáîòêà äàííûõ ôîðìû è îáíîâëåíèå èíôîðìàöèè î ïîëüçîâàòåëå â áàçå äàííûõ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $year = $_POST['year'];
    $gender = $_POST['gender'];
    $limbs = $_POST['limbs'];
    $biography = $_POST['biography'];
    $contract = 1;
 
    $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, year = ?, gender = ?, limbs = ?, biography = ?, contract = ? WHERE id = ?");
    $stmt->execute([$name, $email, $year, $gender, $limbs, $biography, $contract, $user_id]);
 
    header("Location: userinfo.php");
    exit();
}
?>
