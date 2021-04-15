<?php
class UserController extends Controller
{
  public $userModel;

  function __construct()
  {
    require(__DIR__ . '/../models/UserModel.php');
    $this->userModel = new UserModel();
  }

  function register()
  {
    $this->setAttr($this->userModel, 'name', 'POST');
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');
    $this->setAttr($this->userModel, 'photo', 'FILES');
    $this->setAttr($this->userModel, 'address', 'POST');
    $this->setAttr($this->userModel, 'cpf', 'POST');
    $this->setAttr($this->userModel, 'number', 'POST');
    $this->userModel->__set('is_admin', false);

    $this->jsonData($this->userModel->register());
  }

  function login() {
    $this->setAttr($this->userModel, 'email', 'POST');
    $this->setAttr($this->userModel, 'password', 'POST');
    $this->setAttr($this->userModel, 'remember', 'POST');

    $this->jsonData($this->userModel->login());
  }

  function update() {
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

  function index() {
    $this->jsonData($this->userModel->index());
  }

  function show() {
    $this->jsonData($this->userModel->show());
  }

  function logout() {
    session_destroy();
    setcookie('id', '', time() - 3600, '/');
    setcookie('is_admin', '', time() - 3600, '/');

    $this->jsonData(['success', true]);
  }

  function delete() {
    $this->jsonData($this->userModel->delete());

    session_destroy();
    setcookie('id', '', time() - 3600, '/');
    setcookie('is_admin', '', time() - 3600, '/');
  }
}
