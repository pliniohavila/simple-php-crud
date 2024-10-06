<?php 

$route->get('', ['TasksController', 'index']);
$route->get('tasks', ['TasksController', 'index']);
$route->post('tasks', ['TasksController', 'saveTasks']);
$route->put('tasks/{id}', ['TasksController', 'updateTask']);
$route->delete('tasks/{id}', ['TasksController', 'deleteTask']);
$route->patch('tasks/{id}/complete', ['TasksController', 'completeTask']);