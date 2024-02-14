<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Получение данных из формы
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Валидация email с использованием регулярного выражения
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Ошибка: Неверный формат email");
    }

    // Проверка на пустоту
    if (empty($email) || empty($message)) {
        die("Ошибка: Заполните все поля");
    }

    // Безопасность: экранирование данных
    $email = htmlspecialchars($email);
    $message = htmlspecialchars($message);

    // Отправка данных на почту (замените YOUR_EMAIL на реальный адрес)
    $to = "YOUR_EMAIL@example.com";
    $subject = "Новое сообщение от пользователя";
    $headers = "From: $email";

    // Отправка почты
    mail($to, $subject, $message, $headers);

    echo "Спасибо! Ваше сообщение успешно отправлено.";
} else {
    // Если запрос не является POST-запросом, перенаправим на форму
    header("Location: index.html");
}
?>
