<?php

require(__DIR__.'/../models/RequestModel.php');

class EquipmentModel extends Model {

  private $name;
  private $especifications;
  private $photo;
  private $deleted_photo;

  function __get($prop) {
    return $this->$prop;
  }

  function __set($prop, $val) {
    $this->$prop = $val;
  }

  function checkErrors($type) {
    $errors = [];
  
    $checkName = $this->len('nome', 'name', 3, 30);
    if($checkName) $errors[] = $checkName; 

    $checkEspecifications = $this->len('especificações', 'specifications', 50, 500);
    if($checkEspecifications) $errors[] = $checkEspecifications; 

    if($type === 'create') {
      if(!$this->__get('photo')) $errors[] = 'O campo foto é requerido';
    }

    if(sizeof($errors)) {
      return $errors;
    }
        
    return null;
  }

  function addPhoto($id) {
    for($i = 0; $i < sizeof($this->__get('photo')['name']); $i++) {
  
      $ext = explode('.', $this->__get('photo')['name'][$i])[1];
      $filename = date('dmYHis_'.rand(10000, 89999)).'.'.$ext;
      
      $createEquipmentPhotoQuery = 'INSERT INTO equipment_photos (photo, equipment_id) VALUES (?, ?)';
      $createEquipmentPhoto = Connection::connect()->prepare($createEquipmentPhotoQuery);
      $createEquipmentPhoto->bindValue(1, $filename);
      $createEquipmentPhoto->bindValue(2, $id);
      $createEquipmentPhoto->execute();

      move_uploaded_file($this->__get('photo')['tmp_name'][$i], __DIR__.'/../uploads/'. $filename);
      
    }
  }
  
  function findEquipment($includePhotos = false) {
    if(!$includePhotos) {
      $findEquipmentQuery = 'SELECT id FROM equipments WHERE id = ?';

      if($_SESSION['is_admin']) {
        $findEquipmentQuery .= ' LIMIT 1';
      } else {
        $findEquipmentQuery .= ' AND user_id = ? LIMIT 1';
      }

      $findEquipment = Connection::connect()->prepare($findEquipmentQuery);
      $findEquipment->bindValue(1, $_POST['id']);

      if(!$_SESSION['is_admin']) {
        $findEquipment->bindValue(2, $_SESSION['id']);
      }
        
      $findEquipment->execute();
  
      return $findEquipment->fetch(PDO::FETCH_OBJ);
    }
    
    $findEquipmentQuery = 'SELECT equipments.*, GROUP_CONCAT(DISTINCT CONCAT(equipment_photos.id,",",equipment_photos.photo) ORDER BY equipment_photos.id SEPARATOR ";") AS photos FROM equipments INNER JOIN equipment_photos ON equipments.id = equipment_photos.equipment_id WHERE equipments.id = ?';

    if(!$_SESSION['is_admin']) {
      $findEquipmentQuery .= ' AND equipments.user_id = ?';
    }
    
    $findEquipmentQuery .= 'GROUP BY equipments.id LIMIT 1';

    $findEquipment = Connection::connect()->prepare($findEquipmentQuery);
    $findEquipment->bindValue(1, $_POST['id']);

    if(!$_SESSION['is_admin']) {
      $findEquipment->bindValue(2, $_SESSION['id']);
    }
    
    $findEquipment->execute();

    $equipmentData = $findEquipment->fetch(PDO::FETCH_OBJ);

    if(isset($equipmentData->photos)) {
      $equipmentData->photos = explode(';', $equipmentData->photos);

      foreach($equipmentData->photos as $idPhoto => $equip_photo) {
        $id = explode(',', $equip_photo)[0];
        $photo = explode(',', $equip_photo)[1];
        
        $equipmentData->photos[$idPhoto] = ['id' => $id, 'photo' => $photo];
      } 
    }

    return $equipmentData;
  }

  function index($compost = true, $onlyMine = false) {
    if($compost) {
      $selectEquipmentsQuery = 'SELECT users.id as user_id, users.name as user_name, users.photo as user_photo, equipments.*, GROUP_CONCAT(DISTINCT CONCAT(equipment_photos.id,",",equipment_photos.photo) ORDER BY equipment_photos.id SEPARATOR ";") AS photos FROM equipments INNER JOIN equipment_photos ON equipments.id = equipment_photos.equipment_id INNER JOIN users ON users.id = equipments.user_id';
      
      if(!$_SESSION['is_admin'] || $onlyMine) {
        $selectEquipmentsQuery .= ' WHERE users.id = ?';
      }

      $selectEquipmentsQuery .= ' GROUP BY equipments.id';

    } else {
      $selectEquipmentsQuery = 'SELECT id FROM equipments WHERE user_id = ?';
    }
    $selectEquipments = Connection::connect()->prepare($selectEquipmentsQuery);
    if(!$_SESSION['is_admin'] || !$compost || $onlyMine) {
      $selectEquipments->bindValue(1, $_SESSION['id']);
    }
    $selectEquipments->execute();

    $equipmentData = $selectEquipments->fetchAll(PDO::FETCH_OBJ);

    if($compost) {
      foreach($equipmentData as $equipment) {
        if(isset($equipment->photos)) {
          $equipment->photos = explode(';', $equipment->photos);

          foreach($equipment->photos as $idPhoto => $equip_photo) {
            $id = explode(',', $equip_photo)[0];
            $photo = explode(',', $equip_photo)[1];
            
            $equipment->photos[$idPhoto] = ['id' => $id, 'photo' => $photo];
          } 
        }
      }
    }

    return ['success', $equipmentData];    
  }

  function create() {
    try {
      if($this->checkErrors('create')) {
        return ['errors', $this->checkErrors('create')];
        
      }

      $getEquipmentQuery = 'SELECT name FROM equipments WHERE name = ? AND user_id = ? LIMIT 1';
      $getEquipment = Connection::connect()->prepare($getEquipmentQuery);

      $getEquipment->bindValue(1, $this->__get('name'));
      $getEquipment->bindValue(2, $_SESSION['id']);
      
      $getEquipment->execute();

      $equipmentData = $getEquipment->fetch(PDO::FETCH_OBJ);

      if($equipmentData) {
        return ['errors', ['Este equipamento já foi adicionado']];
        
      } 

      $createEquipmentQuery = 'INSERT INTO equipments (name, specifications, user_id) VALUES (?, ?, ?)';
      $createEquipment = Connection::connect()->prepare($createEquipmentQuery);
      $createEquipment->bindValue(1, $this->__get('name'));
      $createEquipment->bindValue(2, $this->__get('specifications'));
      $createEquipment->bindValue(3, $_SESSION['id']);
      $createEquipment->execute();

      $selectEquipmentQuery = 'SELECT id FROM equipments WHERE name = ? LIMIT 1';
      $selectEquipment = Connection::connect()->prepare($selectEquipmentQuery);
      $selectEquipment->bindValue(1, $this->__get('name'));
      $selectEquipment->execute();

      $myEquipment = $selectEquipment->fetch(PDO::FETCH_OBJ);
    
      $this->addPhoto($myEquipment->id);

      return ['success', 'Equipamento criado com sucesso!'];
      

    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function update() {
    try {
      if($this->checkErrors('update')) {
        return ['errors', $this->checkErrors('update')];
      }

      if(isset($this->findEquipment()->id)) {
        $getEquipmentQuery = 'SELECT id FROM equipments WHERE name = ? AND user_id = ? AND id != ? LIMIT 1';
        $getEquipment = Connection::connect()->prepare($getEquipmentQuery);
        $getEquipment->bindValue(1, $this->__get('name'));
        $getEquipment->bindValue(2, $_SESSION['id']);
        $getEquipment->bindValue(3, $_POST['id']);
        $getEquipment->execute();
  
        $equipmentData = $getEquipment->fetch(PDO::FETCH_OBJ);
  
        if(isset($equipmentData->id)) {
          return ['errors', ['Esse equipamento já foi adicionado']];        
        } 
  
        $updateEquipmentQuery = 'UPDATE equipments SET name = ?, specifications = ? WHERE id = ?';
        $updateEquipment = Connection::connect()->prepare($updateEquipmentQuery);
        $updateEquipment->bindValue(1, $this->__get('name'));
        $updateEquipment->bindValue(2, $this->__get('specifications'));
        $updateEquipment->bindValue(3, $_POST['id']);
        $updateEquipment->execute();

        if($this->__get('deleted_photo')) {
          foreach($this->__get('deleted_photo') as $deleted_photo) {
            $findEquipmentPhotoQuery = 'SELECT photo FROM equipment_photos WHERE id = ? AND equipment_id = ?';
            $findEquipmentPhoto = Connection::connect()->prepare($findEquipmentPhotoQuery);
            $findEquipmentPhoto->bindValue(1, $deleted_photo);
            $findEquipmentPhoto->bindValue(2, $_POST['id']);
            $findEquipmentPhoto->execute();
            $myEquipmentPhoto = $findEquipmentPhoto->fetch(PDO::FETCH_OBJ);

            if(isset($myEquipmentPhoto->photo)) {
              unlink(__DIR__. '/../uploads/'.$myEquipmentPhoto->photo);

              $deleteEquipmentPhotoQuery = 'DELETE FROM equipment_photos WHERE id = ? AND equipment_id = ?';
              $deleteEquipmentPhoto = Connection::connect()->prepare($deleteEquipmentPhotoQuery);
              $deleteEquipmentPhoto->bindValue(1, $deleted_photo);
              $deleteEquipmentPhoto->bindValue(2, $_POST['id']);
              $deleteEquipmentPhoto->execute();
            }

          }
        }

        if($this->__get('photo')) {
          $this->addPhoto($_POST['id']);
        } 
        
        return ['success', 'Equipamento atualizado com sucesso!']; 
      }

      return ['errors', ['Equipamento não encontrado']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function delete() {
    try {
      if(isset($this->findEquipment()->id)) {
        $selectEquipmentPhotoQuery = 'SELECT photo FROM equipment_photos WHERE equipment_id = ?';
        $selectEquipmentPhoto = Connection::connect()->prepare($selectEquipmentPhotoQuery);
        $selectEquipmentPhoto->bindValue(1, $_POST['id']);
        $selectEquipmentPhoto->execute();
  
        $equipmentPhotosData = $selectEquipmentPhoto->fetchAll(PDO::FETCH_OBJ);
  
        foreach($equipmentPhotosData as $equipmentPhoto) {
          unlink(__DIR__.'/../uploads/'.$equipmentPhoto->photo);
        }

        $deleteEquipmentQuery = 'DELETE FROM equipments WHERE id = ? LIMIT 1';
        $deleteEquipment = Connection::connect()->prepare($deleteEquipmentQuery);
        $deleteEquipment->bindValue(1, $_POST['id']);
        $deleteEquipment->execute();
        
        return ['success', 'Equipamento deletado com sucesso']; 
      }

      return ['errors', ['Equipamento não encontrado']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function show() {
    $findEquipment = $this->findEquipment(true);
    if(isset($findEquipment->name)) {
      return ['success', $findEquipment];
    }

    return ['errors', ['Equipamento não encontrado']];
  }
}
