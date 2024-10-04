<?php 

$route->get('', ['TasksController', 'index']);
$route->get('tasks', ['TasksController', 'index']);
$route->post('tasks', ['TasksController', 'saveTasks']);
$route->put('tasks/{id}', ['TasksController', 'updateTasks']);

// Tasks Endpoints