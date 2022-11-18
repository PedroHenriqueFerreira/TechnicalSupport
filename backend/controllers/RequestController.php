<?php
class RequestController extends Controller
{
  public $requestModel;
  public $equipmentModel;

  function __construct()
  {
    require(__DIR__ . '/../models/EquipmentModel.php');
    $this->requestModel = new RequestModel();
    $this->equipmentModel = new EquipmentModel();
  }

  function createAction() {
    $this->setAttr($this->requestModel, 'equipment_id', 'POST');
    $this->setAttr($this->requestModel, 'description', 'POST');

    $this->jsonData($this->requestModel->create());
  }

  function create() {
    $this->render(['request', 'create'], $this->equipmentModel->index(true, true));
  }

  function show() {
    $this->render(['request', 'show'], $this->requestModel->show());
  }

  function accept() {
    $this->render(['request', 'accept']);
  }

  function index() {
    $this->render(['request', 'index'], $this->requestModel->index());
  }

  function acceptAction() {
    $this->setAttr($this->requestModel, 'cost', 'POST');
    $this->setAttr($this->requestModel, 'report', 'POST');
    $this->jsonData($this->requestModel->accept());
  }

  function refuseAction() {
    $this->jsonData($this->requestModel->refuse());
  }

  function deleteAction() {
    $this->jsonData($this->requestModel->delete());
  }
}
