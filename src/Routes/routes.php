<?php 

$route->get('', ['TasksController', 'index']);
$route->get('tasks', ['TasksController', 'index']);
$route->post('tasks', ['TasksController', 'saveTasks']);
$route->put('tasks/{id}', ['TasksController', 'updateTasks']);
$route->delete('tasks/{id}', ['TasksController', 'deleteTasks']);
$route->patch('tasks/{id}/complete', ['TasksController', 'completeTasks']);