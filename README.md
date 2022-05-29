# anypay_sci / SCI для anypay.io
Создание платежа:
```php
$sci = new \Payment\AnyPay("project_id", "secret_key");
$sci->create_payment_form([
    "pay_id" => 12345,
    "amount" => 1,
    "desc" => "Тестовая покупка",
    "email" => "test@yandex.ru"
]);
```
Проверка платежа:
```php
$sci = new \Payment\AnyPay("project_id", "secret_key");
if (!$sci->check_ip()) {
    return false;
}

if (!$sci->check_signature($_GET)) {
    return false;
}

// Получение платежа по $_GET["pay_id"]
// Остальные действия...
```
