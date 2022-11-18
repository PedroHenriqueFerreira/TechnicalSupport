<?php
  function format($string, $size) {
    return strlen($string) > $size ? substr($string, 0, $size).'...' : $string;
  }