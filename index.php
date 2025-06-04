<?php
// index.php
header('Content-Type: text/html; charset=UTF-8');
session_start();

// Функции для работы с куками
function setValue($field) {
    return isset($_COOKIE[$field]) ? htmlspecialchars($_COOKIE[$field]) : '';
}

function setChecked($field, $value) {
    return (isset($_COOKIE[$field]) && $_COOKIE[$field] == $value) ? 'checked' : '';
}

function setSelected($field, $value) {
    return (isset($_COOKIE[$field]) && in_array($value, (array)unserialize($_COOKIE[$field])) ? 'selected' : '';
}

// Правила валидации
$validationRules = [
    'fio' => [
        'pattern' => '/^[А-Яа-яЁёA-Za-z\s\-]+$/u',
        'error' => 'ФИО должно содержать только буквы, пробелы и дефисы',
        'max_length' => 150
    ],
    'phone' => [
        'pattern' => '/^\+?\d{11,15}$/',
        'error' => 'Телефон должен быть в формате +71234567890 (11-15 цифр)'
    ],
    'email' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'error' => 'Введите корректный email'
    ],
    'birth_date' => [
        'validate' => function($value) {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            return $date && $date->format('Y-m-d') === $value;
        },
        'error' => 'Введите корректную дату рождения'
    ],
    'gender' => [
        'allowed' => ['male', 'female'],
        'error' => 'Выберите пол'
    ],
    'languages' => [
        'validate' => function($value) {
            return is_array($value) && count($value) > 0;
        },
        'error' => 'Выберите хотя бы один язык программирования'
    ],
    'bio' => [
        'validate' => function($value) {
            return !empty(trim($value));
        },
        'error' => 'Заполните биографию',
        'max_length' => 5000
    ],
    'agreement' => [
        'validate' => function($value) {
            return isset($value);
        },
        'error' => 'Необходимо принять условия'
    ]
];

// Обработка GET запроса
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = [];
    
    // Сообщение об успешном сохранении
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Данные успешно сохранены!</div>';
    }
    
    // Ошибки валидации
    $errors = [];
    foreach (array_keys($validationRules) as $field) {
        if (!empty($_COOKIE[$field.'_error'])) {
            $errors[$field] = $_COOKIE[$field.'_error'];
            setcookie($field.'_error', '', time() - 3600);
        }
    }
    
    include('form.php');
    exit();
}

// Обработка POST запроса
$errors = FALSE;

foreach ($validationRules as $field => $rule) {
    $value = $_POST[$field] ?? null;
    
    // Проверка на пустое значение для обязательных полей
    if (empty($value) && $value !== '0') {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
        continue;
    }
    
    // Проверка по регулярному выражению
    if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
    }
    
    // Проверка с помощью filter_var
    if (isset($rule['filter']) && !filter_var($value, $rule['filter'])) {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
    }
    
    // Проверка с помощью callback-функции
    if (isset($rule['validate']) && !$rule['validate']($value)) {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
    }
    
    // Проверка максимальной длины
    if (isset($rule['max_length']) && mb_strlen($value) > $rule['max_length']) {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
    }
    
    // Проверка допустимых значений
    if (isset($rule['allowed']) && !in_array($value, $rule['allowed'])) {
        setcookie($field.'_error', $rule['error'], 0);
        $errors = TRUE;
    }
    
    // Если ошибок нет, сохраняем значение в куки на год
    if (!isset($_COOKIE[$field.'_error'])) {
        $cookieValue = is_array($value) ? serialize($value) : $value;
        setcookie($field.'_value', $cookieValue, time() + 365 * 24 * 60 * 60);
    }
}

if ($errors) {
    header('Location: index.php');
    exit();
}

// Сохранение в базу данных
try {
    $db = new PDO('mysql:host=localhost;dbname=u68653', 'u68653', '7251537', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $db->beginTransaction();
    
    // Сохраняем основную информацию
    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birth_date, gender, bio, agreement) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['bio'],
        1
    ]);
    
    $app_id = $db->lastInsertId();
    
    // Сохраняем выбранные языки программирования
    $stmt = $db->prepare("INSERT INTO application_languages (app_id, lang_id) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang) {
        $stmt->execute([$app_id, (int)$lang]);
    }
    
    $db->commit();
    
    setcookie('save', '1', time() + 365 * 24 * 60 * 60);
    header('Location: index.php');
} catch (PDOException $e) {
    $db->rollBack();
    die('Ошибка при сохранении данных: ' . $e->getMessage());
}
