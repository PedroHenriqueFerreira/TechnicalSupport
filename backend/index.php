<?php

require(__DIR__ . '/config/connection.php');
require(__DIR__.'/utils/Model.php');
require(__DIR__.'/utils/Controller.php');
require(__DIR__.'/utils/cryptography.php');
require(__DIR__.'/utils/importImg.php');
require(__DIR__.'/utils/format.php');

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

    define('URL', $this->url);

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
        $this->routes['*'][1],
      );
    } else {
      if(!isset($_SESSION['id']) && isset($_COOKIE['id'])) {
        $_SESSION['id'] = cryptography($_COOKIE['id'], 'decrypt');
        $_SESSION['name'] = cryptography($_COOKIE['name'], 'decrypt');
        $_SESSION['photo'] = cryptography($_COOKIE['photo'], 'decrypt');
        $_SESSION['is_admin'] = cryptography($_COOKIE['is_admin'], 'decrypt');
      }

      if(isset($this->routes[$this->url][2]) && $this->routes[$this->url][2] && !isset($_SESSION['id'])) {
        if(isset($_GET['reduced'])) {
          echo json_encode(['errors' => ['Login requerido']]);
        } else {
          $this->control('home', 'redirect', ['redirect', '/login', 'Login requerido']);
        }
        return null;
      } else {
        if(
          isset($this->routes[$this->url][2]) && !$this->routes[$this->url][2] && 
          isset($this->routes[$this->url][3]) && $this->routes[$this->url][3] && 
          isset($_SESSION['id'])
        ) {
          if(isset($_GET['reduced'])) {
            echo json_encode(['errors' => ['Saia da conta atual para acessar esta página']]);          
          } else {
            $this->control('home', 'redirect', ['redirect', '/', 'Saia da conta atual para acessar esta página']);
          }
          return null;
        }

        if(
          isset($this->routes[$this->url][2]) && $this->routes[$this->url][2] && 
          isset($this->routes[$this->url][3]) && $this->routes[$this->url][3] && 
          isset($_SESSION['is_admin']) && !$_SESSION['is_admin']
        ) {
          if(isset($_GET['reduced'])) {
            echo json_encode(['errors' => ['Autorização necessária']]);          
          } else {
            $this->control('home', 'redirect', ['redirect', '/', 'Autorização necessária']);
          }
          return null;
        }
        
        $this->control(
          $this->routes[$this->url][0],
          $this->routes[$this->url][1],
        );  
      }
    }
  }

  function control($page, $action, $response = '')
  {
    require(__DIR__ . '/controllers' . '/' . ucfirst($page) . 'Controller.php');

    $controller = ucfirst($page) . 'Controller';
    
    if($response) {
      (new $controller)->$action($response);
    } else {
      (new $controller)->$action();
    }
  }
}

$routes = new Routes();
