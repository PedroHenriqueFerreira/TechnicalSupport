<?php
$myRoutes = [
// route => [class, func, isLogged, isRestricted],
  '*' => ['home', 'error', false],
  '/' => ['home', 'index', false],

  '/user/register' => ['user', 'register', false, true],
  '/user/login' => ['user', 'login', false, true],

  '/user/index' => ['user', 'index', true, true],
  '/user/update' => ['user', 'update', true],
  '/user/show' => ['user', 'show', true],
  '/user/delete' => ['user', 'delete', true],
  '/user/logout' => ['user', 'logout', true],

  '/equipment/create' => ['equipment', 'create', true],
  '/equipment/index' => ['equipment', 'index', true],
  '/equipment/:id/update' => ['equipment', 'update', true],
  '/equipment/:id/show' => ['equipment', 'show', true],
  '/equipment/:id/delete' => ['equipment', 'delete', true],

  '/request/create' => ['request', 'create', true],
  '/request/index' => ['request', 'index', true],
  '/request/:id/show' => ['request', 'show', true],
  '/request/:id/update' => ['request', 'update', true, true],
  '/request/:id/delete' => ['request', 'delete', true],
];
