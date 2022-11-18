<?php
class UserController extends Controller
{
  public $userModel;

  function __construct()
  {
    require(__DIR__ . '/../models/UserModel.php');
    $this->userModel = new UserModel();
  }

  function registerAction()
  {
    $this->setAttr($this->userModel, 'name', 'POST');
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');
    $this->userModel->__set('is_admin', false);
    $this->setAttr($this->userModel, 'photo', 'FILES');
    $this->setAttr($this->userModel, 'address', 'POST');
    $this->setAttr($this->userModel, 'cpf', 'POST');
    $this->setAttr($this->userModel, 'number', 'POST');
    
    $this->jsonData($this->userModel->register());
  }
  
  function checkRegisterAction() {
    $this->setAttr($this->userModel, 'name', 'POST');
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');

    $this->jsonData($this->userModel->checkRegister());
  }

  function loginAction() {
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');
    $this->setAttr($this->userModel, 'remember', 'POST');

    $this->jsonData($this->userModel->login());
  }

  function updateAction() {
    $this->setAttr($this->userModel, 'name', 'POST');
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');
    $this->setAttr($this->userModel, 'new_password', 'POST');
    $this->setAttr($this->userModel, 'photo', 'FILES');
    $this->setAttr($this->userModel, 'address', 'POST');
    $this->setAttr($this->userModel, 'cpf', 'POST');
    $this->setAttr($this->userModel, 'number', 'POST');
    $this->setAttr($this->userModel, 'deleted_number', 'POST');

    $this->jsonData($this->userModel->update());
  }

  function technicals() {
    $this->render(['user', 'users'], $this->userModel->index(true));
  }
  
  function clients() {
    $this->render(['user', 'users'], $this->userModel->index());
  }

  function logoutAction() {
    session_destroy();
    setcookie('id', '', time() - 3600, '/');
    setcookie('name', '', time() - 3600, '/');
    setcookie('photo', '', time() - 3600, '/');
    setcookie('is_admin', '', time() - 3600, '/');

    $this->jsonData(['success', 'Usuário deslogado com sucesso!']);
  }

  function deleteAction() {
    $this->jsonData($this->userModel->delete());

    session_destroy();
    setcookie('id', '', time() - 3600, '/');
    setcookie('name', '', time() - 3600, '/');
    setcookie('photo', '', time() - 3600, '/');
    setcookie('is_admin', '', time() - 3600, '/');
  }

  function login() {
    $this->render(['user', 'login']);
  }

  function register() {
    $this->render(['user', 'register']);
  }

  function profile() {
    $this->render(['user', 'profile'], $this->userModel->profile(true));
  }

  function show() {
    $this->render(['user', 'show'], $this->userModel->profile());
  }
}
