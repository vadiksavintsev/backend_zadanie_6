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
 
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;
 
// Îáðàáîòêà äàííûõ ôîðìû è îáíîâëåíèå èíôîðìàöèè î ïîëüçîâàòåëå â áàçå äàííûõ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $year = $_POST['year'];
    $gender = $_POST['gender'];
    $limbs = $_POST['limbs'];
    $biography = $_POST['biography'];
    $contract = isset($_POST['contract']) ? 1 : 0;
 
    // Âàëèäàöèÿ äàííûõ
    if (!preg_match('/^[\p{L}\s]+$/u', $name)) {
    $errors[] = "Field name contains forbidden symbols";
}
 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@.*\.ru$/', $email)) {
        $errors[] = "E-mail should be in format example@example.ru";
    }
 
 
    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, year = ?, gender = ?, limbs = ?, biography = ?, contract = ? WHERE id = ?");
        $stmt->execute([$name, $email, $year, $gender, $limbs, $biography, $contract, $user_id]);
        $success = true;
    }
}
 
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Info</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container">
        <h1>Edit info</h1>
 
       <?php if (!empty($errors)) {
    echo '<div class="error-container">';
    foreach ($errors as $error) {
        echo '<p class="error"> ' . $error . '</p>';
    }
    echo '</div>';
} ?>

 
        <?php if (empty($errors)) {
    echo '<div class="success-container">';
    echo '<p class="success"> Saved Successfully </p>';
    echo '</div>';
} ?>
 
        <form action="userinfo.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?= $user['name'] ?>" required> <br/>
 
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" value="<?= $user['email'] ?>" required> <br/>
 
            <label for="birth_year">Year:</label>
            <input type="number" name="year" id="year" value="<?= $user['year'] ?>" min="1923" max="2023" required> <br/>
 
            <label>Gender:</label>
            <label><input type="radio" name="gender" value="male" <?= $user['gender'] == 'male' ? 'checked' : '' ?> required> Male</label>
            <label><input type="radio" name="gender" value="female" <?= $user['gender'] == 'female' ? 'checked' : '' ?> required> Female</label> <br/>
 
            <label>Num of limbs:</label>
<!--             <input type="number" name="limbs" id="limbs" value="<?= $user['limbs'] ?>" min="1" max="4" required> <br/> -->
            <label><input type="radio" checked="checked" name="limbs" id="limbs" value="1" <?= $user['limbs'] == '1' ? 'checked' : '' ?> required>1</label>
            <label><input type="radio" name="limbs" id="limbs" value="2" <?= $user['limbs'] == '2' ? 'checked' : '' ?> required>2</label>
            <label><input type="radio" name="limbs" id="limbs" value="3" <?= $user['limbs'] == '3' ? 'checked' : '' ?> required>3</label> 
            <label><input type="radio" name="limbs" id="limbs" value="4" <?= $user['limbs'] == '4' ? 'checked' : '' ?> required>4</label><br/>
 
            <label for="biography">Biography:</label>
            <textarea name="biography" id="biography" required><?= $user['biography'] ?></textarea> <br/>
            <label>
                <input type="checkbox" name="contract" value="accepted" <?= $user['contract'] == 'accepted' ? 'checked' : '' ?> required > С контрактом ознакомлен</label><br/>

 
            <input type="submit" value="Save changes">
        </form>
        <p><a href="quitlog.php">Logout</a></p>
    </div>
</body>
</html>
