<!DOCTYPE html>
<!-- form.php -->
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анкета</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        .form-container {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .radio-group {
            display: flex;
            gap: 15px;
        }
        .radio-option {
            display: flex;
            align-items: center;
        }
        .radio-option input {
            margin-right: 5px;
        }
        input[type="submit"] {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background: #2980b9;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        .error-field {
            border-color: #e74c3c !important;
        }
        .success {
            color: #27ae60;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .messages {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Анкета</h2>
        
        <div class="messages">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $message): ?>
                    <?= $message ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <form action="index.php" method="POST">
            <div class="form-group">
                <label>ФИО: 
                    <input type="text" name="fio" class="<?= !empty($errors['fio']) ? 'error-field' : '' ?>" 
                           value="<?= setValue('fio_value') ?>" required>
                </label>
                <?php if (!empty($errors['fio'])): ?>
                    <div class="error"><?= $errors['fio'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Телефон: 
                    <input type="tel" name="phone" class="<?= !empty($errors['phone']) ? 'error-field' : '' ?>" 
                           value="<?= setValue('phone_value') ?>" required>
                </label>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error"><?= $errors['phone'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email: 
                    <input type="email" name="email" class="<?= !empty($errors['email']) ? 'error-field' : '' ?>" 
                           value="<?= setValue('email_value') ?>" required>
                </label>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Дата рождения: 
                    <input type="date" name="birth_date" class="<?= !empty($errors['birth_date']) ? 'error-field' : '' ?>" 
                           value="<?= setValue('birth_date_value') ?>" required>
                </label>
                <?php if (!empty($errors['birth_date'])): ?>
                    <div class="error"><?= $errors['birth_date'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="male" name="gender" value="male" <?= setChecked('gender_value', 'male') ?> required>
                        <label for="male">Мужской</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="female" name="gender" value="female" <?= setChecked('gender_value', 'female') ?>>
                        <label for="female">Женский</label>
                    </div>
                </div>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error"><?= $errors['gender'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Любимый язык программирования:</label>
                <select name="languages[]" multiple required class="<?= !empty($errors['languages']) ? 'error-field' : '' ?>">
                    <option value="1" <?= setSelected('languages_value', '1') ?>>Pascal</option>
                    <option value="2" <?= setSelected('languages_value', '2') ?>>C</option>
                    <option value="3" <?= setSelected('languages_value', '3') ?>>C++</option>
                    <option value="4" <?= setSelected('languages_value', '4') ?>>JavaScript</option>
                    <option value="5" <?= setSelected('languages_value', '5') ?>>PHP</option>
                    <option value="6" <?= setSelected('languages_value', '6') ?>>Python</option>
                    <option value="7" <?= setSelected('languages_value', '7') ?>>Java</option>
                    <option value="8" <?= setSelected('languages_value', '8') ?>>Haskell</option>
                    <option value="9" <?= setSelected('languages_value', '9') ?>>Clojure</option>
                    <option value="10" <?= setSelected('languages_value', '10') ?>>Prolog</option>
                    <option value="11" <?= setSelected('languages_value', '11') ?>>Scala</option>
                    <option value="12" <?= setSelected('languages_value', '12') ?>>Go</option>
                </select>
                <?php if (!empty($errors['languages'])): ?>
                    <div class="error"><?= $errors['languages'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Биография:</label>
                <textarea name="bio" rows="5" required class="<?= !empty($errors['bio']) ? 'error-field' : '' ?>"><?= setValue('bio_value') ?></textarea>
                <?php if (!empty($errors['bio'])): ?>
                    <div class="error"><?= $errors['bio'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="agreement" <?= setValue('agreement_value') ? 'checked' : '' ?> required>
                    С условиями контракта ознакомлен
                </label>
                <?php if (!empty($errors['agreement'])): ?>
                    <div class="error"><?= $errors['agreement'] ?></div>
                <?php endif; ?>
            </div>

            <input type="submit" value="Сохранить">
        </form>
    </div>
</body>
</html>
