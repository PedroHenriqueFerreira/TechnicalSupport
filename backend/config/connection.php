<?php
class Connection
{
  static function connect()
  {
    try {
      $host = 'localhost';
      $db = 'assistance';
      $user = 'root';
      $password = '';
      
      return new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $password);
    } catch (Throwable $e) {
      return die($e);
    }
  }
}
