<?php declare(strict_types = 1);

namespace App\Controllers;

use \Datetime;
use App\Domain\TaskDTO;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\TasksServices;
use App\Traits\HttpResponse;

class TasksController 
{
    use HttpResponse;
    
    private TasksServices $tasksServices;

    public function __construct() 
    {
        $this->tasksServices = new TasksServices();
    }

    public function index() 
    {
        $title = $_GET['title'] ?? null; 
        $description = $_GET['description'] ?? null; 

        $params = [
            'title' => $title, 
            'description' => $description
        ];

        try {
            $tasks = $this->tasksServices->getTasks($params); 

            return $this->jsonResponse(200, $tasks);
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function saveTasks() 
    {
        try {
            $payload = file_get_contents('php://input');
            $data = json_decode($payload, true);

            if (!isset($data['title']) || empty($data['title']) || !is_string($data['title'])) 
                throw ValidationException::missingField("title");

            if (!isset($data['description']) || empty($data['description']) || !is_string($data['description']))
                throw ValidationException::missingField("description");

            // (new DateTime())->format('Y-m-d H:i:s')
            $task = new TaskDTO(null, $data['title'], $data['description'], new DateTime(date("Y-m-d h:i:s")));
            $savedTaskId = $this->tasksServices->saveTasks($task);
            if (!$savedTaskId) 
                throw new \Exception('An error occurred when trying to add new tasks');

            return $this->jsonResponse(201, [
                'message' => 'Post created successfully.', 
                'Tasks ID' => $savedTaskId
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getCode(), 'Validation Error', $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function updateTasks()
    {
        try {
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $pathSegments = array_filter(explode('/', $url));
            $id = $pathSegments[2] ?? -1;

            $payload = file_get_contents('php://input');
            $data = json_decode($payload, true);

            $checkTitleIsSend = !isset($data['title']) || empty($data['title']) || !is_string($data['title']);
            $checkDescriptionIsSend = !isset($data['description']) || empty($data['description']) || !is_string($data['description']);
      
           
            if (!is_numeric($id))
                throw ValidationException::idShouldBeAnIntegerValue();

            if ($checkTitleIsSend && $checkDescriptionIsSend)
                throw ValidationException::missingFieldToUpdate();
            
            $updatedTask = $this->tasksServices->updateTasks($id, $data);

            return $this->jsonResponse(200, [
                'message' => 'Task updated successfully.', 
                'Tasks' => $updatedTask
            ]);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getCode(), 'Validation Error', $e->getMessage());
        } catch (NotFoundException $e) {
            return $this->errorResponse($e->getCode(), 'Task Not Found', $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }
}