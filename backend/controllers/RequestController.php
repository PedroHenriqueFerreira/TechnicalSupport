<?php
class RequestController extends Controller
{
  public $requestModel;

  function __construct()
  {
    require(__DIR__ . '/../models/RequestModel.php');
    $this->requestModel = new RequestModel();
  }

  function create() {
    $this->setAttr($this->requestModel, 'equipment_id', 'POST');
    $this->setAttr($this->requestModel, 'description', 'POST');

    $this->jsonData($this->requestModel->create());
  }

  function show() {
    $this->jsonData($this->requestModel->show());
  }

  function index() {
    $this->jsonData($this->requestModel->index());
  }

  function update() {
    $this->setAttr($this->requestModel, 'status', 'POST');
    $this->setAttr($this->requestModel, 'cost', 'POST');
    $this->setAttr($this->requestModel, 'report', 'POST');
    $this->jsonData($this->requestModel->update());
  }

  function delete() {
    $this->jsonData($this->requestModel->delete());
  }
}
