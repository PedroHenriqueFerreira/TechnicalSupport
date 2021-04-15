<?php

require(__DIR__.'/../models/EquipmentModel.php');

class UserModel extends Model {

  private $name;
  private $email;
  private $cpf;
  private $address;
  private $password;
  private $new_password;
  private $number;
  private $deleted_number;

  function __get($prop) {
    return $this->$prop;
  }

  function __set($prop, $val) {
    $this->$prop = $val;
  }

  function checkErrors($type) {
    $errors = [];
  
    if($type !== 'login') {
      $checkCPF = $this->find('cpf', 'cpf', ['.', '.', '-']);
      if($checkCPF) $errors[] = $checkCPF; 
    
      $checkCPF2 = $this->len('cpf', 'cpf', 14, 0);
      if($checkCPF2) $errors[] = $checkCPF2; 

      $checkAddress = $this->len('endereço', 'address', 3, 50);
      if($checkAddress) $errors[] = $checkAddress; 
    
      $checkName = $this->len('nome', 'name', 3, 30);
      if($checkName) $errors[] = $checkName;
    }

    $checkEmail = $this->find('email', 'email', ['@', '.com']);
    if($checkEmail) $errors[] = $checkEmail;

    if($type !== 'update') {
      $checkPass = $this->len('senha', 'password', 5, 32);
      if($checkPass) $errors[] = $checkPass;
    }
    
    if($type === 'update') {
      if($this->__get('password') && $this->__get('new_password')) {
        if($this->__get('new_password') === $this->__get('password')) $errors[] = 'As senhas devem ser distintas';
  
        $checkPass = $this->len('senha atual', 'password', 5, 32);
        if($checkPass) $errors[] = $checkPass;

        $checkNewPass = $this->len('senha nova', 'new_password', 5, 32);
        if($checkNewPass) $errors[] = $checkNewPass;

      } else if(
          ($this->__get('password') && !$this->__get('new_password')) || 
          (!$this->__get('password') && $this->__get('new_password'))
      ) {
        $errors[] = 'Os campos senha e nova senha são requeridos';
      }
    }

    if($type === 'register') {
      if(!$this->__get('photo')) {
        $errors[] = 'O campo foto é requerido';
      }
    }

    if($type === 'register' || $type === 'update') {
      if(($type === 'update' && $this->__get('number')) || $type === 'register') {
        $checkNumber = $this->find('número', 'number', ['+', '(', ')', '-']);
        if($checkNumber) $errors[] = $checkNumber;
  
        $checkNumber2 = $this->len('número', 'number', 19, 0);
        if($checkNumber2) $errors[] = $checkNumber2;
      }
    }

    if(sizeof($errors)) {
      return $errors;
    }
        
    return null;
  }

  function deleteNumbers() {
    if($this->__get('deleted_number')) {
      foreach($this->__get('deleted_number') as $number) {
        $deleteNumbersQuery = 'DELETE FROM user_numbers WHERE user_id = ? AND id = ?';
        $deleteNumbers = Connection::connect()->prepare($deleteNumbersQuery);
        $deleteNumbers->bindValue(1, $_SESSION['id']);
        $deleteNumbers->bindValue(2, $number);
        $deleteNumbers->execute();
      }

      return null;
    }
  }

  function createNumbers($myIdData) {
    foreach($this->__get('number') as $number) {
      $insertNumberQuery = 'INSERT INTO user_numbers (phone_number, user_id) VALUES (?, ?)';

      $insertNumber = Connection::connect()->prepare($insertNumberQuery);

      $insertNumber->bindValue(1, $number);
      $insertNumber->bindValue(2, $myIdData);
      $insertNumber->execute();
    }
  }

  function deleteMyEquipments() {
    $equipmentModel = new EquipmentModel();

    foreach($equipmentModel->index(false)[1] as $myEquipment) {
      $_POST['id'] = $myEquipment->id;
      $equipmentModel->delete();
    }
  }

  function register() {
    try {
      if($this->checkErrors('register')) {
        return ['errors', $this->checkErrors('register')];
      }

      $ext = explode('.', $this->__get('photo')['name'])[1];
      $filename = date('dmYHis_'.rand(10000, 89999)).'.'.$ext;

      $getUserQuery = 'SELECT email FROM users WHERE cpf = ? OR email = ? LIMIT 1';

      $getUser = Connection::connect()->prepare($getUserQuery);

      $getUser->bindValue(1, $this->__get('cpf'));
      $getUser->bindValue(2, $this->__get('email'));
      $getUser->execute();

      $userData = $getUser->fetch(PDO::FETCH_OBJ);

      if(!isset($userData->email)) {
        $registerUserQuery = 'INSERT INTO users (photo, name, email, password, cpf, address, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $registerUser = Connection::connect()->prepare($registerUserQuery);
        $registerUser->bindValue(1, $filename);
        $registerUser->bindValue(2, $this->__get('name'));
        $registerUser->bindValue(3, $this->__get('email'));
        $registerUser->bindValue(4, password_hash($this->__get('password'), PASSWORD_DEFAULT));
        $registerUser->bindValue(5, $this->__get('cpf'));
        $registerUser->bindValue(6, $this->__get('address'));
        $registerUser->bindValue(7, 0);
        $registerUser->execute();
    
        move_uploaded_file($this->__get('photo')['tmp_name'], __DIR__.'/../uploads/'. $filename);
        
        $myIdQuery = 'SELECT id FROM users WHERE email = ?';
        $myId = Connection::connect()->prepare($myIdQuery);
        $myId->bindValue(1, $this->__get('email'));

        $myId->execute();

        $myIdData = $myId->fetch(PDO::FETCH_OBJ);

        if(isset($myIdData->id)) {
          $this->createNumbers($myIdData->id);
        }

        return ['success', true];
      } 

      return ['errors', ['Este usuário já foi criado']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function update() {
    try {
      if($this->checkErrors('update')) {
        return ['errors', $this->checkErrors('update')];
      }

      $getUserQuery = 'SELECT email FROM users WHERE (cpf = ? OR email = ?) AND id != ? LIMIT 1';
      $getUser = Connection::connect()->prepare($getUserQuery);
      $getUser->bindValue(1, $this->__get('cpf'));
      $getUser->bindValue(2, $this->__get('email'));
      $getUser->bindValue(3, $_SESSION['id']);
      
      $getUser->execute();

      $userData = $getUser->fetch(PDO::FETCH_OBJ);

      if(!$userData) {
        $this->deleteNumbers();

        $updateUserQuery = 'UPDATE users SET name = ?, email = ?, cpf = ?, address = ? WHERE id = ?';

        if($this->__get('photo')) {
          $updateUserQuery = 'UPDATE users SET name = ?, email = ?, cpf = ?, address = ?, photo = ? WHERE id = ?';

          $myUserQuery = 'SELECT photo FROM users WHERE id = ?';
          $myUser = Connection::connect()->prepare($myUserQuery);
          $myUser->bindValue(1, $_SESSION['id']);
          $myUser->execute();
          
          $myPhoto = $myUser->fetch(PDO::FETCH_OBJ);

          if($myPhoto) {
            unlink(__DIR__.'/../uploads/'.$myPhoto->photo);
          }

          $ext = explode('.', $this->__get('photo')['name'])[1];
          $filename = date('dmYHis_'.rand(10000, 89999)).'.'.$ext;
          move_uploaded_file($this->__get('photo')['tmp_name'], __DIR__.'/../uploads/'. $filename);
        }

        $updateUser = Connection::connect()->prepare($updateUserQuery);
        $updateUser->bindValue(1, $this->__get('name'));
        $updateUser->bindValue(2, $this->__get('email'));
        $updateUser->bindValue(3, $this->__get('cpf'));
        $updateUser->bindValue(4, $this->__get('address'));

        if($this->__get('photo')) {
          $updateUser->bindValue(5, $filename);
          $updateUser->bindValue(6, $_SESSION['id']);
        } else {
          $updateUser->bindValue(5, $_SESSION['id']);
        }

        $updateUser->execute();

        if($this->__get('number')) $this->createNumbers($_SESSION['id']);

        if($this->__get('password')) {
          $findByPassQuery = 'SELECT id, password FROM users WHERE id = ? LIMIT 1';
          $findByPass = Connection::connect()->prepare($findByPassQuery);
          $findByPass->bindValue(1, $_SESSION['id']);
          $findByPass->execute();
  
          $foundPass = $findByPass->fetch(PDO::FETCH_OBJ);

          if(isset($foundPass->id) && password_verify($this->__get('password'), $foundPass->password)) {
            $updatePassQuery = 'UPDATE users SET password = ? WHERE id = ?';
            $updatePass = Connection::connect()->prepare($updatePassQuery);
            $updatePass->bindValue(1, password_hash($this->__get('new_password'), PASSWORD_DEFAULT));
            $updatePass->bindValue(2, $_SESSION['id']);
            $updatePass->execute();
            
            return ['success', true];
          } 

          return ['errors', ['Senha atual inválida']];
        }

        return ['success', true];
      } 

      if($userData->email === $this->__get('email')) {
        return ['errors', ['Este email já está em uso por outra pessoa']];
      } 
        
      return ['errors', ['Este CPF já é de outra pessoa']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function login() {
    try {
      if($this->checkErrors('login')) {
        return ['errors', $this->checkErrors('login')];
      }

      $getUserQuery = 'SELECT id, is_admin, password FROM users WHERE email = ? LIMIT 1';

      $getUser = Connection::connect()->prepare($getUserQuery);
      $getUser->bindValue(1, $this->__get('email'));
      $getUser->execute();

      $userData = $getUser->fetch(PDO::FETCH_OBJ);

      if(!$userData || !password_verify($this->__get('password'), $userData->password)) {
        return ['errors', ['Email e/ou senha inválidos']];
      } 
      
      $_SESSION['id'] = $userData->id;
      $_SESSION['is_admin'] = $userData->is_admin;

      if($this->__get('remember')) {
        setcookie('id', cryptography($userData->id), time() + (3600 * 24 * 7), '/');
        setcookie('is_admin', cryptography($userData->is_admin), time() + (3600 * 24 * 7), '/');
      }
        
      return ['success', true];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }
  
  function index() {
    try {

      $usersQuery = 'SELECT users.photo, users.name, users.email, users.cpf, users.address, GROUP_CONCAT(DISTINCT CONCAT(user_numbers.id,",",user_numbers.phone_number) ORDER BY user_numbers.id SEPARATOR ";") AS numbers FROM users INNER JOIN user_numbers ON users.id = user_numbers.user_id GROUP BY users.id';
      
      $users = Connection::connect()->prepare($usersQuery);
      $users->execute();
  
      $usersData = $users->fetchAll(PDO::FETCH_OBJ);

      if($usersData) {
        foreach($usersData as $userData) {
          if(isset($userData->numbers)) {
            $userData->numbers = explode(';', $userData->numbers);
  
            foreach($userData->numbers as $idNumber => $user_number) {
              $id = explode(',', $user_number)[0];
              $number = explode(',', $user_number)[1];
              
              $userData->numbers[$idNumber] = ['id' => $id, 'number' => $number];
            } 
          }
        }
      }    
  
      return ['success', $usersData];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function show() {
    try {

      $myUserQuery = 'SELECT users.photo, users.name, users.email, users.cpf, users.address, GROUP_CONCAT(DISTINCT CONCAT(user_numbers.id,",",user_numbers.phone_number) ORDER BY user_numbers.id SEPARATOR ";") AS numbers FROM users INNER JOIN user_numbers ON users.id = user_numbers.user_id WHERE users.id = ? GROUP BY users.id';

      $myUser = Connection::connect()->prepare($myUserQuery);
      $myUser->bindValue(1, $_SESSION['id']);
      $myUser->execute();
  
      $myUserData = $myUser->fetch(PDO::FETCH_OBJ);

      if($myUserData) {

        if(isset($myUserData->numbers)) {
          $myUserData->numbers = explode(';', $myUserData->numbers);

          foreach($myUserData->numbers as $idNumber => $user_number) {
            $id = explode(',', $user_number)[0];
            $number = explode(',', $user_number)[1];
            
            $myUserData->numbers[$idNumber] = ['id' => $id, 'number' => $number];
          } 
        }

        return ['success', $myUserData];
      }
  
      return ['errors', ['Este usuário não existe']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }

  function delete() {
    try {

      $getUserQuery = 'SELECT photo, id FROM users WHERE id = ?';
      $getUser = Connection::connect()->prepare($getUserQuery);
      $getUser->bindValue(1, $_SESSION['id']);
      $getUser->execute();
      
      $userData = $getUser->fetch(PDO::FETCH_OBJ);

      if($userData) {
        if(isset($userData->photo)) {
          unlink(__DIR__.'/../uploads/'.$userData->photo);
        }

        $this->deleteMyEquipments();

        $deleteUserQuery = 'DELETE FROM users WHERE id = ?';
        $deleteUser = Connection::connect()->prepare($deleteUserQuery);
        $deleteUser->bindValue(1, $_SESSION['id']);
        $deleteUser->execute();   
        
        return ['success', true];
      }

      return ['errors', ['Este usuário não existe']];
    } catch(Throwable $e) {
      return ['errors', [$e->getMessage()]];
    }
  }
}