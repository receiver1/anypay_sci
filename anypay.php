<?php

namespace Payment;

class AnyPay
{
  const VALUE_SEPARATOR = ":";
  const DEFAULT_ALGORITHM = "sha256";
  const CREATE_URI = "https://anypay.io/merchant";

  protected $project_id;
  protected $secret_key;

  public function __construct($project_id, $secret_key)
  {
    $this->project_id = $project_id;
    $this->secret_key = $secret_key;
  }

  /**
   * Генерация сигнатуры платежа
   * @param array Параметры платежа
   * @return string Хеш-сумма параметров
   */
  public function generate_signature(array $params) 
  {
    $params["secret_key"] = $this->secret_key; // Добавляем секретный ключ
    return hash(self::DEFAULT_ALGORITHM, implode(self::VALUE_SEPARATOR, $params));
  }

  /**
   * Проверка сигнатуры платежа
   * @param array Параметры платежа
   * @return bool Является ли сигнатура верной
   */
  public function check_signature(array $params)
  {
    $arr_sign = 
    [
      $params["currency"], 
      $params["amount"], 
      $params["pay_id"],
      $this->project_id,
      "paid",
    ];

    $sign = $this->generate_signature($arr_sign);
    return $params["sign"] == $sign;
  }

  /**
   * Создание платёжной формы
   * @param array Параметры платежа
   * @return string Ссылка на форму
   */
  public function create_payment_form(array $params)
  {
    $default = 
    [
      "merchant_id" => $this->project_id,
      "pay_id" => null,
      "amount" => null,
      "currency" => "RUB",
      "desc" => null,
      "success_url" => null,
      "fail_url" => null
    ];

    $intersect = array_intersect_key($params, $default);        // Извлекаем одинаковые ключи
    $replaced = array_replace_recursive($default, $intersect);  // Заменяем в default ключи на ключи из intersect
    $diff = array_diff_key($params, $replaced);                 // Извлекаем дополнительные параметры
    $params = array_merge($replaced, $diff);                    // Сливаем массивы

    $params["sign"] = $this->generate_signature($replaced);
    return self::CREATE_URI."?".http_build_query($params, "", "&", PHP_QUERY_RFC3986);
  }

  /**
   * Проверка IP запроса
   * @return bool Принадлежит ли IP AnyPay
   */
  public function check_ip()
  {
    $ips_array = 
    [
      "185.162.128.38", 
      "185.162.128.39", 
      "185.162.128.88"
    ];

    $ip = $_SERVER["REMOTE_ADDR"];
    return in_array($ip, $ips_array);
  }
}