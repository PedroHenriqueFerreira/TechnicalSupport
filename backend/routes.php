<?php
$myRoutes = [
//route => [class, func, isLogged, isRestricted],
  '*' => ['home', 'error', false],
  '/' => ['request', 'index', true],

  // USERS

  '/user/action/register' => ['user', 'registerAction', false, true],
  '/user/action/check/register' => ['user', 'checkRegisterAction', false, true],
  '/register' => ['user', 'register', false, true],

  '/user/action/login' => ['user', 'loginAction', false, true],
  '/login' => ['user', 'login', false, true],
  
  '/user/action/update' => ['user', 'updateAction', true],
  '/user/action/logout' => ['user', 'logoutAction', true],
  '/user/action/delete' => ['user', 'deleteAction', true],
  '/profile' => ['user', 'profile', true],
  '/profile/:id' => ['user', 'show', true, true],
  
  '/clients' => ['user', 'clients', true, true],
  '/technicals' => ['user', 'technicals', true],

  // EQUIPMENTS

  '/equipment/action/create' => ['equipment', 'createAction', true],
  '/equipment/create' => ['equipment', 'create', true],

  '/equipment/action/:id/update' => ['equipment', 'updateAction', true],
  '/equipment/:id' => ['equipment', 'show', true],
  
  '/equipment/action/:id/delete' => ['equipment', 'delete', true],
  '/equipments' => ['equipment', 'index', true],

  // REQUESTS

  '/request/action/create' => ['request', 'createAction', true],
  '/request/create' => ['request', 'create', true],  

  '/request/:id' => ['request', 'show', true],
  
  '/request/action/:id/accept' => ['request', 'acceptAction', true, true],
  '/request/action/:id/refuse' => ['request', 'refuseAction', true, true],
  '/request/:id/accept' => ['request', 'accept', true, true],

  '/request/action/:id/delete' => ['request', 'deleteAction', true],

  // OTHES

  '/about' => ['home', 'about', false],
];
