<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'submit.php'; ?>

    <div class="container">
        <?php
        if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
            echo '<div class="errors">';
            foreach ($_SESSION['errors'] as $error) {
                echo '<p>' . $error . '</p>';
            }
            echo '</div>';
        } elseif (isset($_COOKIE['name'])) {
            echo '<div class="success">';
            echo '<p>Форма успешно отправлена</p>';
            echo '</div>';
        }
        ?>

        <h1>Суперспособности</h1>
        <form action="submit.php" method="POST" id="form">
            <!-- Добавьте остальные поля формы с сохранением значений из cookie -->
            <label for="name">Имя:</label>
            <?= showError('name') ?>
            <input type="text" name="name" id="name" value="<?= getFieldValue('name') ?>">

            <label for="email">E-mail:</label>
            <?= showError('email') ?>
            <input type="text" name="email" id="email" value="<?= getFieldValue('email') ?>">

           <label for="year">Год рождения:</label>
           <?= showError('year') ?>
            <select name="year" id="year" >
                <option value="<?= getSelected('year', "") ?>">Выберите год</option>
                <!-- Заполните значения для годов -->
            </select>
            <label>Пол:</label>
            <?= showError('gender') ?>
            <label><input type="radio" checked="checked" name="gender" value="male" <?= getChecked('gender', 'male') ?>> Мужской</label>
            <label><input type="radio" name="gender" value="female" <?= getChecked('gender', 'female') ?>> Женский</label>

            <label>Количество конечностей:</label>
            <?= showError('limbs') ?>
            <label><input type="radio" checked="checked" name="limbs" id="limbs" value="1" <?= getChecked('limbs', '1') ?>> 1</label>
            <label><input type="radio" name="limbs" id="limbs" value="2" <?= getChecked('limbs', '2') ?>> 2</label>
            <label><input type="radio" name="limbs" id="limbs" value="3" <?= getChecked('limbs', '3') ?>> 3</label>
            <label><input type="radio" name="limbs" id="limbs" value="4" <?= getChecked('limbs', '4') ?>> 4</label>
            <label for="abilities">Сверхспособности:</label>
            <select name="power[]" id="power" multiple>
                 <option value="invisibility" <?= getSelected('power', 'invisibility') ?>>Невидимость</option>
                    <option value="stoppingtime" <?= getSelected('power', 'stoppingtime') ?>>Остановка времени</option>
                    <option value="ignition" <?= getSelected('power', 'ignition') ?>>Воспламенение</option>
                    <option value="elements" <?= getSelected('power', 'elements') ?>>Управление стихиями</option>
               </select>
            <?php if (!empty($messages['power'])) {print($messages['power']);}?>

            <label for="biography">Биография:</label>
            <textarea name="biography" id="biography"><?= getFieldValue('biography') ?></textarea>

            <label>
                <input type="checkbox" name="contract" value="accepted" <?= getChecked('contract', 'accepted') ?>> С контрактом ознакомлен
            </label>

            <input type="submit" value="Отправить">
            <a href="login.php" class="auth-button">Авторизация</a>
        </form>
          <script>
              const select = document.getElementById('year');
              const currentYear = new Date().getFullYear();
              for (let i = currentYear; i >= currentYear - 100; i--) {
                  const option = document.createElement('option');
                  option.value = i;
                  option.text = i;
                  if(i == <?= isset($_COOKIE['year']) ? $_COOKIE['year'] : '""' ?>) 
                     {
                     option.selected = true; // выбираем этот элемент, если год сохранен в куке
                     }
                  select.add(option);
}

    </script>
    </div>
</body>
</html>
