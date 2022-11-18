<?php
class EquipmentController extends Controller
{
  public $equipmentModel;

  function __construct()
  {
    require(__DIR__ . '/../models/EquipmentModel.php');
    $this->equipmentModel = new EquipmentModel();
  }

  function createAction() {
    $this->setAttr($this->equipmentModel, 'name', 'POST');
    $this->setAttr($this->equipmentModel, 'specifications', 'POST');
    $this->setAttr($this->equipmentModel, 'photo', 'FILES');

    $this->jsonData($this->equipmentModel->create());
  }

  function create() {
    $this->render(['equipment', 'create']);
  }

  function index() {
    $this->render(['equipment', 'index'], $this->equipmentModel->index(true));
  }

  function updateAction() {
    $this->setAttr($this->equipmentModel, 'name', 'POST');
    $this->setAttr($this->equipmentModel, 'specifications', 'POST');
    $this->setAttr($this->equipmentModel, 'photo', 'FILES');
    $this->setAttr($this->equipmentModel, 'deleted_photo', 'POST');

    $this->jsonData($this->equipmentModel->update());
  }

  function show() {
    $this->render(['equipment', 'show'], $this->equipmentModel->show());
  }

  function delete() {
    $this->jsonData($this->equipmentModel->delete());
  }
}
