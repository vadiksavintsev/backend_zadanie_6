<?php
session_start();

function showError($field)
{
    if (isset($_SESSION['errors'][$field])) {
        return '<span class="error">' . $_SESSION['errors'][$field] . '</span>';
    }
    return '';
}

function getSelected($fieldName, $value)
{
    if (isset($_COOKIE[$fieldName]) && in_array($value, explode(',', $_COOKIE[$fieldName]))) {
        return 'selected';
    }
    return '';
}
if(isset($_POST['year'])) {
  $selectedYear = $_POST['year'];
  setcookie('year', $selectedYear, time() + (86400 * 30), "/"); // сохраняем куку на 30 дней
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Получаем выбранные значения и записываем их в куки-файл
  if (isset($_POST["power"])) {
    $selectedA = implode(',', $_POST["abilities"]);
    setcookie('power', $selectedA, time() + 3600, '/');
  }
}

function getChecked($fieldName, $value)
{
    if (isset($_COOKIE[$fieldName]) && $_COOKIE[$fieldName] == $value) {
        return 'checked';
    }
    return '';
}
 if (isset($_POST["gender"])) {
    $value = $_POST["gender"];
    setcookie('gender', $value, time() + 3600, '/');
  }
if (isset($_POST["limbs"])) {
    $value = $_POST["limbs"];
    setcookie('limbs', $value, time() + 3600, '/');
  }



function getFieldValue($fieldName)
{
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors']) && isset($_SESSION['data'][$fieldName])) {
        return htmlspecialchars($_SESSION['data'][$fieldName]);
    } elseif (isset($_COOKIE[$fieldName])) {
        return htmlspecialchars($_COOKIE[$fieldName]);
    }
    return '';
}

// Настройки подключения к базе данных
$servername = "localhost";
$username = "u52989";
$password = "5004286";
$dbname = "u52989";

// Создание подключения
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получение данных из формы
    $name = $_POST["name"];
    $email = $_POST["email"];
    $year = $_POST["year"];
    $gender = $_POST["gender"];
    $limbs = $_POST["limbs"];
    $power = $_POST["power"];
    $biography = $_POST["biography"];
    $contract = $_POST["contract"] == "accepted";

    // Валидация данных
    $errors = [];
    $_SESSION['errors'] = [];

    // Валидация полей (проверка на пустоту и корректность)
    if (empty($name)) 
    {
    $errors[] = "Поле Имя не должно быть пустым.";
    $nameClass = "errorsi"; // добавляем класс error, если есть ошибка
} else {
  $nameClass = ""; // очищаем класс, если ошибки нет
}

    

if (empty($email)) {
    $errors[] = "Поле E-mail не должно быть пустым.";
}

if (empty($year)) {
    $errors[] = "Поле Год рождения не должно быть пустым.";
}

if (empty($gender)) {
    $errors[] = "Поле Пол не должно быть пустым.";
}

if (empty($limbs)) {
    $errors[] = "Поле Количество конечностей не должно быть пустым.";
}
if (!empty($name) && !preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $name)) {
    $errors[] = "Имя содержит недопустимые символы. Допустимо использовать буквы русского и английского алфавитов";
}
 
if (!empty($email) && (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/.*@.*\.ru$/", $email))) {
    $errors[] = "Неверный формат e-mail.";
}




if(!$contract){
  $errors[] = "Пожалуйста ознакомьтесь с правилами.";
}

$_SESSION['data'] = [
    'name' => $name,
    'email' => $email,
    'year' => $year,
    'gender' => $gender,
    'limbs' => $limbs,
    'power' => $power,
    'biography' => $biography,
    'contract' => $contract
];
    // Сохранение данных, если нет ошибок
    if (empty($errors)) {
        unset($_SESSION['errors']);

         try {
            $stmt = $db->prepare("INSERT INTO users (name, email, year, gender, limbs, biography, contract) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $year, $gender, $limbs, $biography, $contract]);
 
            $last_index = $db->lastInsertId();
 
            $login = 's' . sprintf('%07d', mt_rand(0, 9999999));
            $password = bin2hex(random_bytes(8));
            $hased_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO user_auth (user_id, login, password) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $login, $hased_password]);
            
            $stmt = $db->prepare("SELECT id_power FROM power WHERE power = ?");
            foreach ($powers as $value) {
            $stmt->execute([$value]);
            $id_power = $stmt->fetchColumn();
 
            $stmt2 = $db->prepare("INSERT INTO namepower (id_person, id_power) VALUES (?, ?)");
            $stmt2->execute([$last_index, $id_power]);
            }
            
            $_SESSION['login'] = $login;
            $_SESSION['password'] = $password;
            $_SESSION['user_id'] = $user_id;

            $cookie_expires = time() + 60 * 60 * 24 * 365;
            setcookie('name', $name, $cookie_expires);
            setcookie('email', $email, $cookie_expires);
            setcookie('year', $year, $cookie_expires);
            setcookie('gender', $gender, $cookie_expires);
            setcookie('limbs', $limbs, $cookie_expires);
            setcookie('power', implode(',', $power), $cookie_expires);
            setcookie('biography', $biography, $cookie_expires);
            setcookie('contract', $contract, $cookie_expires);
            unset($_SESSION['data']);

            header("Location: userdata.php");
            exit();
        } catch (PDOException $e) {
            print('Error : ' . $e->getMessage());
            exit();
        }
    } else {
        foreach ($errors as $field => $error) {
            $_SESSION['errors'][$field] = $error;
        }
        header("Location: index.php");
        exit();
    }
}
