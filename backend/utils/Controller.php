<?php
class Controller
{
  function setAttr($model, $param, $method) {
    if($method === 'POST') $method = $_POST;
    if($method === 'GET') $method = $_GET;
    if($method === 'FILES') $method = $_FILES;

    $model->__set($param, isset($method[$param]) ? $method[$param] : '');
  }

  function jsonData($data) {
    echo json_encode([$data[0] => $data[1]]);
    return null;
  }

  function render($page, $result = '') {
    if($result !== '' && $result[0] === 'errors') {
      require(__DIR__ . '/../views/components/Header.phtml');
      require(__DIR__ . '/../views/Home/Error.phtml');
      require(__DIR__ . '/../views/components/Footer.phtml');  
    } else {
      require(__DIR__ . '/../views/components/Header.phtml');
      require(__DIR__ . '/../views/' . ucfirst($page[0]) . '/' . ucfirst($page[1]) . '.phtml');
      require(__DIR__ . '/../views/components/Footer.phtml');
    }
  }
}
