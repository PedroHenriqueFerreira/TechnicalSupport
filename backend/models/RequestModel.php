<?php

class RequestModel extends Model {

  private $equipment_id;
  private $cost;
  private $status;
  private $report;
  private $description;

  function __get($prop) {
    return $this->$prop;
  }

  function __set($prop, $val) {
    $this->$prop = $val;
  }

  function checkErrors($type) {
    $errors = [];
  
    if($type === 'create') {
      if(!$this->__get('equipment_id')) $errors[] = 'O campo id do equipamento é requerido';

      $checkDesc = $this->len('descrição', 'description', 50, 500);
      if($checkDesc) $errors[] = $checkDesc; 
    } else {
      if($this->__get('status') != 1 && $this->__get('status') != 2 && $this->__get('status') != 3) $errors[] = 'Valor inválido para o campo status';

      if(!$this->__get('cost')) $errors[] = 'O campo custo é requerido';

      $checkReport = $this->len('relatório', 'report', 50, 500);
      if($checkReport) $errors[] = $checkReport; 
    }


    if(sizeof($errors)) {
      return $errors;
    }
        
    return null;
  }

  function findEquipment($id, $compost = true, $requestId = false) {
    if($compost) {
      if($requestId) {
        $getEquipmentQuery = 'SELECT requests.id FROM equipments INNER JOIN requests ON equipments.id = requests.equipment_id WHERE requests.id = ?';
      } else {
        $getEquipmentQuery = 'SELECT requests.id FROM equipments INNER JOIN requests ON equipments.id = requests.equipment_id WHERE equipments.id = ?';
      }
    } else {
      if($requestId) {
        $getEquipmentQuery = 'SELECT equipments.id FROM equipments INNER JOIN requests ON equipments.id = requests.equipment_id WHERE requests.id = ?';
      } else {
        if($_SESSION['is_admin']) {
          $getEquipmentQuery = 'SELECT id FROM equipments WHERE id = ? LIMIT 1';
        } else {
          $getEquipmentQuery = 'SELECT id FROM equipments WHERE id = ? AND user_id = ? LIMIT 1';
        }
      }
    }

    if($compost || $requestId) {
      if($_SESSION['is_admin']) {
        $getEquipmentQuery .=  ' LIMIT 1';
      } else {
        $getEquipmentQuery .=  ' AND equipments.user_id = ? LIMIT 1';
      }
    }

    $getEquipment = Connection::connect()->prepare($getEquipmentQuery);
    $getEquipment->bindValue(1, $id);
    if(!$_SESSION['is_admin']) {
      $getEquipment->bindValue(2, $_SESSION['id']);
    }
    $getEquipment->execute();

    return $getEquipment->fetch(PDO::FETCH_OBJ);
  }

  function create() {
    try {

      if($this->checkErrors('create')) {
        return ['errors', $this->checkErrors('create')];
      }

      $findEquipment = $this->findEquipment($this->__get('equipment_id'), false);

      if(isset($findEquipment->id)) {
        $createRequestQuery = 'INSERT INTO requests (description, equipment_id, status, cost, report) VALUES (?, ?, ?, ?, ?)';
        $createRequest = Connection::connect()->prepare($createRequestQuery);

        $createRequest->bindValue(1, $this->__get('description'));
        $createRequest->bindValue(2, $this->__get('equipment_id'));
        $createRequest->bindValue(3, 0);
        $createRequest->bindValue(4, 0);
        $createRequest->bindValue(5, '');

        $createRequest->execute();

        return ['success', true];
      }

      return ['errors', ['Equipamento não encontrado']];
      return null;
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function show() {
    try {
      $getRequestQuery = 'SELECT requests.id, equipments.name, equipments.specifications, requests.created_at, requests.updated_at, requests.status, requests.cost, requests.report, requests.description FROM requests INNER JOIN equipments ON equipments.id = requests.equipment_id WHERE requests.id = ?';

      if(!$_SESSION['is_admin']) {
        $getRequestQuery .= ' AND equipments.user_id = ?';
      }

      $getRequest = Connection::connect()->prepare($getRequestQuery);
      $getRequest->bindValue(1, $_POST['id']);
      if(!$_SESSION['is_admin']) {
        $getRequest->bindValue(2, $_SESSION['id']);
      }

      $getRequest->execute();

      $request = $getRequest->fetch(PDO::FETCH_OBJ);
      if($request) {
        return ['success', $request];
      } 

      return ['errors', ['Ordem de serviço não encontrada']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function index() {
    try {
      $getRequestsQuery = 'SELECT requests.id, equipments.name, equipments.specifications, requests.created_at, requests.updated_at, requests.status, requests.cost, requests.report, requests.description FROM requests INNER JOIN equipments ON equipments.id = requests.equipment_id';

      if(!$_SESSION['is_admin']) {
        $getRequestsQuery .= ' WHERE equipments.user_id = ?';
      }

      $getRequests = Connection::connect()->prepare($getRequestsQuery);
      if(!$_SESSION['is_admin']) {
        $getRequests->bindValue(1, $_SESSION['id']);
      }
      
      $getRequests->execute();

      $allRequests = $getRequests->fetchAll(PDO::FETCH_OBJ);

      return ['success', $allRequests];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function update() {
    try {
      if($this->checkErrors('update')) {
        return ['errors', $this->checkErrors('update')];
      }

      $findEquipment = $this->findEquipment($_POST['id'], true, true);

      if(isset($findEquipment->id)) {
        $updateEquipmentQuery = 'UPDATE requests SET cost = ?, report = ?, status = ? WHERE id = ?';
        $updateEquipment = Connection::connect()->prepare($updateEquipmentQuery);
        $updateEquipment->bindValue(1, $this->__get('cost'));
        $updateEquipment->bindValue(2, $this->__get('report'));
        $updateEquipment->bindValue(3, $this->__get('status'));
        $updateEquipment->bindValue(4, $_POST['id']);
        $updateEquipment->execute();

        return ['success', true];
      }

      return ['errors', ['Ordem de serviço não encontrada']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function delete() {
    try {
      $findEquipment = $this->findEquipment($_POST['id'], true, true);

      if(isset($findEquipment->id)) {
        $deleteRequestQuery = 'DELETE FROM requests WHERE id = ?';
        $deleteRequest = Connection::connect()->prepare($deleteRequestQuery);
        $deleteRequest->bindValue(1, $_POST['id']);
        $deleteRequest->execute();

        return ['success', true];
      }

      return ['errors', ['Ordem de serviço não encontrada']];
    } catch(Throwable $e) {
      return ['errors', $e->getMessage()];
    }
  }
}
