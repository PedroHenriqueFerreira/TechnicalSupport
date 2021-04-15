<?php
class EquipmentController extends Controller
{
  public $equipmentModel;

  function __construct()
  {
    require(__DIR__ . '/../models/EquipmentModel.php');
    $this->equipmentModel = new EquipmentModel();
  }

  function create() {
    $this->setAttr($this->equipmentModel, 'name', 'POST');
    $this->setAttr($this->equipmentModel, 'specifications', 'POST');
    $this->setAttr($this->equipmentModel, 'photo', 'FILES');

    $this->jsonData($this->equipmentModel->create());
  }

  function index() {
    $this->jsonData($this->equipmentModel->index());
  }

  function update() {
    $this->setAttr($this->equipmentModel, 'name', 'POST');
    $this->setAttr($this->equipmentModel, 'specifications', 'POST');
    $this->setAttr($this->equipmentModel, 'photo', 'FILES');
    $this->setAttr($this->equipmentModel, 'deleted_photo', 'POST');

    $this->jsonData($this->equipmentModel->update());
  }

  function show() {
    $this->jsonData($this->equipmentModel->show());
  }

  function delete() {
    $this->jsonData($this->equipmentModel->delete());
  }
}
