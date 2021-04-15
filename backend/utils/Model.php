<?php

class Model {
  function __get($prop) {
    return $this->$prop;
  }

  function __set($prop, $val) {
    $this->$prop = $val;
  }
  
  function len($attr_name, $attr, $min, $max) {
    if($this->__get($attr)) {
      if(is_array($this->__get($attr))) {

        foreach($this->__get($attr) as $item) {
          if($max !== 0) {
            if(strlen($item) < $min || strlen($item) > $max) {
              return "O campo $attr_name precisa ter entre $min e $max caracteres";
            }
          } else {
            if(strlen($item) !== $min) {
              return "O campo $attr_name precisa ter $min caracteres";
            }
          }
        }

      } else {
        if($max !== 0) {
          if(strlen($this->__get($attr)) < $min || strlen($this->__get($attr)) > $max) {
            return "O campo $attr_name precisa ter entre $min e $max caracteres";
          }
          return null;
        } else {
          if(strlen($this->__get($attr)) !== $min) {
            return "O campo $attr_name precisa ter $min caracteres";
          }
          return null;
        }
      }
    } else {
      return "O campo $attr_name é requerido";
    }

  }

  function find($attr_name, $attr, $words) {
    $errors = '';

    if($this->__get($attr)) {
      if(is_array($this->__get($attr))) {

        foreach($this->__get($attr) as $item) {
          foreach($words as $word) {
            if(!strrpos($item, $word) && strrpos($item, $word) !== 0) {
              $errors .= " $word";
              $attr = substr_replace($word, '', $errors);
            }
          }
      
          if($errors) {
            return "O $attr_name precisa ter$errors";
          }  
        }

      } else {
        foreach($words as $word) {
          if(!strrpos($this->__get($attr), $word)) {
            $errors .= " $word";
            $attr = substr_replace($word, '', $errors);
          }
        }
    
        if($errors) {
          return "O $attr_name precisa ter$errors";
        }
        return null;  
      }
    } else {
      return "O campo $attr_name é requerido";
    }
      
  }
}