<?php
require(__DIR__ . '/config/connection.php');
require(__DIR__.'/utils/Model.php');
require(__DIR__.'/utils/Controller.php');
require(__DIR__.'/utils/cryptography.php');

class Routes
{
  public $url;
  public $routes;

  function __construct()
  {

    $this->url = parse_url(
      $_SERVER['REQUEST_URI'],
      PHP_URL_PATH
    );

    require(__DIR__ . '/routes.php');
    $this->routes = $myRoutes;

    session_start();

    $this->controllers();
  }

  function controllers()
  {

    if (filter_var($this->url, FILTER_SANITIZE_NUMBER_INT)) {
      $_POST['id'] = filter_var($this->url, FILTER_SANITIZE_NUMBER_INT);

      $this->url = implode(
        ':id',
        explode(
          filter_var(
            $this->url,
            FILTER_SANITIZE_NUMBER_INT
          ),
          $this->url
        )
      );
    }

    $found = false;

    foreach ($this->routes as $i => $v) {
      if ($i == $this->url) {
        $found = true;
      }
    }


    if (!$found) {
      $this->control(
        $this->routes['*'][0],
        $this->routes['*'][1]
      );
    } else {
      if(!isset($_SESSION['id']) && isset($_COOKIE['id'])) {
        echo 'DESCRIPTOGRAFANDO...';
        $_SESSION['id'] = cryptography($_COOKIE['id'], 'decrypt');
        $_SESSION['is_admin'] = cryptography($_COOKIE['is_admin'], 'decrypt');
      }

      if(isset($this->routes[$this->url][2]) && $this->routes[$this->url][2] && !isset($_SESSION['id'])) {
        echo json_encode(['errors' => ['Login requerido']]);
        return null;
      } else {
        if(
          isset($this->routes[$this->url][2]) && !$this->routes[$this->url][2] && 
          isset($this->routes[$this->url][3]) && $this->routes[$this->url][3] && 
          isset($_SESSION['id'])
        ) {
          echo json_encode(['errors' => ['Saia de conta para acessar essa função']]);          
          return null;
        }

        if(
          isset($this->routes[$this->url][2]) && $this->routes[$this->url][2] && 
          isset($this->routes[$this->url][3]) && $this->routes[$this->url][3] && 
          isset($_SESSION['is_admin']) && !$_SESSION['is_admin']
        ) {
          echo json_encode(['errors' => ['Autorização necessária']]);          
          return null;
        }

        $this->control(
          $this->routes[$this->url][0],
          $this->routes[$this->url][1]
        );  
      }
    }
  }

  function control($page, $action)
  {
    require(__DIR__ . '/controllers' . '/' . ucfirst($page) . 'Controller.php');

    $controller = ucfirst($page) . 'Controller';
    (new $controller)->$action();
  }
}

$routes = new Routes();
