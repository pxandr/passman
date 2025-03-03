<?php

//данные хранятся тут
define('DATA_FILE', 'passwords.json');

//Шифруем вот так, как по учебнику, лучше черех .env говорят
define('ENCRYPTION_KEY', trim(file_get_contents('.env')));

// 
function encryptData($data)
{
    $iv = random_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Расшифровываем 
function decryptData($data)
{
    $decoded = base64_decode($data);
    $iv = substr($decoded, 0, 16);
    $encrypted = substr($decoded, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
}

// Загружаем пароли из json
function loadPasswords()
{
    return file_exists(DATA_FILE) ? json_decode(file_get_contents(DATA_FILE), true) : [];
}

// Сохраняем пароли
function savePasswords($passwords)
{
    file_put_contents(DATA_FILE, json_encode($passwords, JSON_PRETTY_PRINT));
}

// Добавляем пароль
function addPassword($service, $password)
{
    $passwords = loadPasswords();
    $passwords[$service] = encryptData($password);
    savePasswords($passwords);
    echo "Пароль для '$service' сохранен.\n";
}

// Получаем пароль
function getPassword($service)
{
    $passwords = loadPasswords();
    if (isset($passwords[$service])) 
    {
        echo "Пароль для '$service': " . decryptData($passwords[$service]) . "\n";
    } 
    else 
    {
        echo "Сервис '$service' не найден.\n";
    }
}

// Тут мы удаляем пассы лишние
function deletePassword($service)
{
    $passwords = loadPasswords();
    if (isset($passwords[$service])) 
    {
        unset($passwords[$service]);
        savePasswords($passwords);
        echo "Пароль для '$service' удален.\n";
    } 
    else 
    {
        echo "Сервис '$service' не найден.\n";
    }
}

// Список сервисов которые мы накидали
function listServices()
{
    $passwords = loadPasswords();
    echo "Сохраненные сервисы:\n";
    foreach (array_keys($passwords) as $service) 
    {
        echo " - $service\n";
    }
}

//как использовать
if ($argc < 2) 
{
    echo "Использование:\n";
    echo "  php passman.php add <service> <password>\n";
    echo "  php passman.php get <service>\n";
    echo "  php passman.php delete <service>\n";
    echo "  php passman.php list\n";
    exit(1);
}

$command = $argv[1];

switch ($command) 
{
    case 'add':
        if ($argc < 4) 
        {
            echo "Ошибка: укажите сервис и пароль.\n";
            exit(1);
        }
        addPassword($argv[2], $argv[3]);
        break;
    case 'get':
        if ($argc < 3) 
        {
            echo "Ошибка: укажите сервис.\n";
            exit(1);
        }
        getPassword($argv[2]);
        break;
    case 'delete':
        if ($argc < 3) 
        {
            echo "Ошибка: укажите сервис.\n";
            exit(1);
        }
        deletePassword($argv[2]);
        break;
    case 'list':
        listServices();
        break;
    default:
        echo "Неизвестная команда.\n";
}
