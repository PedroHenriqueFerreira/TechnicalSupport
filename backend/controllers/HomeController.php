<?php
class HomeController extends Controller
{
  function __construct()
  {
  }

  function error()
  {
    $this->render(['home', 'error']);
  }

  function about() {
    $this->render(['home', 'about']);
  }

  function redirect($response) {
    $this->render(['home', 'redirect'], $response);
  }
}
