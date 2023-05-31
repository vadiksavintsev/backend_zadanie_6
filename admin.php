<?php


if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Òðåáóåòñÿ àâòîðèçàöèÿ</h1>');
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

if (isset($_POST['delete_btn'])) {
    $userIdToDelete = $_POST['delete_id'];
 
    try {
        $stmt = $db->prepare("DELETE FROM namepower WHERE id_person = ?");
        $stmt->execute([$userIdToDelete]);

        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userIdToDelete]);

        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        echo "Îøèáêà ïðè óäàëåíèè ïîëüçîâàòåëÿ: " . $e->getMessage();
    }
}
$sql = "SELECT * FROM users";
$result = $db->query($sql);
$users = $result->fetchAll(PDO::FETCH_ASSOC);

$abilities_sql = "SELECT * FROM power";
$abilities_result = $db->query($abilities_sql);
$abilities = $abilities_result->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    foreach ($users as $user) {
        $id = $user['id'];
        $name = $_POST['name'][$id];
        $email = $_POST['email'][$id];
        $year = $_POST['year'][$id];
        $gender = $_POST['gender'][$id];
        $limbs = $_POST['limbs'][$id];
        $biography = $_POST['biography'][$id];
        $contract = 1;

        $update_sql = "UPDATE users SET name = ?, email = ?, year = ?, gender = ?, limbs = ?, biography = ?, contract = ? WHERE id = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([$name, $email, $year, $gender, $limbs, $biography, $contract, $id]);

        $delete_abilities_sql = "DELETE FROM namepower WHERE id_person = ?";
        $delete_abilities_stmt = $db->prepare($delete_abilities_sql);
        $delete_abilities_stmt->execute([$id]);

        foreach ($power as $ability) {
            if (isset($_POST['power'][$id]) && in_array($ability['id_power'], $_POST['power'][$id])) {
                $insert_abilities_sql = "INSERT INTO namepower (id_person, id_power) VALUES (?, ?)";
                $insert_abilities_stmt = $db->prepare($insert_abilities_sql);
                $insert_abilities_stmt->execute([$id, $ability['id_power']]);
            }
        }
    }
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Page</title>
<style>
table {
border-collapse: collapse;
width: 100%;
}
th, td {
border: 1px solid black;
padding: 8px;
text-align: left;
}
th {
background-color: #f2f2f2;
}
input[type="checkbox"] {
transform: scale(1.5);
}
.delete-button {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        cursor: pointer;
    }
</style>
</head>
<body>
    <h1>Admin</h1>
<form action="admin.php" method="post">
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Birth year</th>
            <th>Gender</th>
            <th>Num of limbs</th>
            <th>Bio</th>
            <th>Abilities</th>
            <th>REMOVE</th>
        </tr>
        <?php foreach ($users as $user) : ?>
            <?php
            $user_id = $user['id'];
            $user_abilities_sql = "SELECT id_power FROM namepower WHERE id_person = ?";
            $user_abilities_stmt = $db->prepare($user_abilities_sql);
            $user_abilities_stmt->execute([$user_id]);
            $user_abilities = $user_abilities_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            ?>
            <tr>
                <td><input type="text" name="name[<?= $user_id ?>]" value="<?= htmlspecialchars($user['name']) ?>"></td>
                <td><input type="text" name="email[<?= $user_id ?>]" value="<?= htmlspecialchars($user['email']) ?>"></td>
                <td><input type="number" name="year[<?= $user_id ?>]" value="<?= $user['year'] ?>" min="1900" max="2023"></td>
                <td>
                    <select name="gender[<?= $user_id ?>]">
                        <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>female</option>
                    </select>
                </td>
                <td><input type="number" name="limbs[<?= $user_id ?>]" value="<?= $user['limbs'] ?>" min="1" max="4"></td>
                <td><textarea name="biography[<?= $user_id ?>]"><?= htmlspecialchars($user['biography']) ?></textarea></td>
            
                <td>
                    <?php foreach ($powers as $power)
                        <div>
                            <input type="checkbox" name="powers[<?= $user_id ?>][]" value="<?= $power['id'] ?>" <?= in_array($power['id'], $user_powers) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($power['power']) ?>
                        </div>
                    <?php endforeach; ?>
                </td>
                <td>
                <form method="POST">
                  <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                  <button type="submit" name="delete_btn" class="delete-button">Remove</button>
                </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>
    <button type="submit">Save</button>
</form>

<h2>Stats</h2>
<table border="1">
    <tr>
        <th>Ability</th>
        <th>Num of users</th>
    </tr>
    <?php
    $sql = "SELECT p.power, COUNT(ua.id_person) AS user_count
            FROM power p
            JOIN namepower ua ON p.id_power = ua.id_power
            GROUP BY p.id_power";
    $stmt = $db->query($sql);
    $abilities_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    foreach ($abilities_stats as $ability_stat) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($ability_stat['power']) . "</td>";
        echo "<td>" . htmlspecialchars($ability_stat['user_count']) . "</td>";
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
