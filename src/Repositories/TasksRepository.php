<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Domain\TaskDTO;
use App\Core\Database;
use App\Domain\Link;
use App\Exceptions\NotFoundException;
use DateTime;
use PDO;

class TasksRepository
{
    protected $pdo;

    public function __construct() {
        $this->pdo = Database::connection();
    }

    /**
     * @param string $table The name of the table.
     * @param array $conditions Conditions to the where clause
     * @return TaskDTO[]
     */
    public function getTasks(string $table, array $conditions = []): array 
    {
        $query = "select * from {$table}";
        $conditions = array_filter($conditions);

        if (!empty($conditions)) 
            $query .= $this->processConditions($conditions);
       
        $stmt = $this->pdo->prepare($query);
        foreach ($conditions as &$condition) {
            $condition = "%{$condition}%";
        };
        
        $stmt->execute($conditions);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $tasks = array_map(function ($taskData) {
            $task = $this->taskArrayToTaskDTO($taskData);
            return $task;
        }, $results);

        return $tasks;
    }

    public function findById(int $id): TaskDTO
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $task =  $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task)
            throw NotFoundException::taskNotFoundById($id);

        return $this->taskArrayToTaskDTO($task);
    }

    /**
     * @param string $table The name of the table.
     * @param TaskDTO $data DTO of the tasks to be save
     * @return string|false
     */
    public function insert(string $table, TaskDTO $data): string 
    {
        $table = "$table";
        $tasksArray = array_filter(get_object_vars($data));
        $tasksArray['createdAt'] = ((array) $tasksArray['createdAt'])['date'];

        $keys = $this->prepareColumName(implode(', ', array_keys($tasksArray)));

        $query = "INSERT INTO {$table} ({$keys})";
        $query .= $this->processInsertedData($tasksArray);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    public function update($id, string $table, array $data): TaskDTO
    {
        $task = $this->findById((int)$id);

        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        if (isset($data['title'])) 
            $task->title = $data['title'];
        if (isset($data['description'])) 
            $task->description = $data['description'];
       
        $query = "UPDATE {$table} SET ";
        $query .= $this->processUpdateData($data);
        $query .= " WHERE ID = :id";
        
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($data);

        $taskUpdated =  $this->findById((int)$id);
        return $taskUpdated;
    }

    public function delete($id, string $table): bool
    {
        $task = $this->findById((int)$id);

        $query = "DELETE FROM {$table} WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    public function completeTask($id, string $table): bool
    {
        $task = $this->findById((int)$id);

        $now = (new DateTime())->format('Y-m-d H:i:s');
        $data['completed_at'] = null;
        $data['updated_at'] = $now;
        $data['id'] = $task->id;

        if (!$task->completedAt)
            $data['completed_at'] = $now;
        
        $query = "UPDATE {$table} SET completed_at = :completed_at, updated_at = :updated_at WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($data);
    }

    private function processConditions($conditions): string
    {
        $attributes = array_keys($conditions);
        $sql = implode(" AND ", array_map(fn ($attr) => "$attr LIKE :$attr", $attributes));
        return " WHERE {$sql}";
    }
    

    private function processInsertedData($data): string 
    {
        $attributes = array_keys($data);
        $sql = implode(", ", array_map(fn ($attr) => "'{$data[$attr]}'", $attributes));

        return " VALUES({$sql})";
    }

    private function processUpdateData($data): string
    {
        $attributes = array_keys($data);    
        $sql = implode(", ", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        return $sql;
    }

    private function prepareColumName(string $str): string 
    {
        $r = "";
        foreach (str_split($str) as $s) {
            if (ord($s) >= 65 && ord($s) <= 90) {
                $r .= '_';
                $s = strtolower($s);
            }
            $r .= $s;
        }
        return $r;
    }

    private function taskArrayToTaskDTO(array $taskArray): TaskDTO
    {
        $task = new TaskDTO(
            $taskArray['id'] ?? null,
            $taskArray['title'],
            $taskArray['description'],
            new DateTime($taskArray['created_at']),
            isset($taskArray['updated_at']) ? new DateTime($taskArray['updated_at']) : null,
            isset($taskArray['completed_at']) ? new DateTime($taskArray['completed_at']) : null 
        );

        $taskId = $task->id ?? null;
        
        $task->addLink(new Link("/tasks", 'GET', 'Get all tasks'));
        $task->addLink(new Link("/tasks?title={title}&description={description}", 'GET', 'GET tasks by title or description (send both or one them)'));
        $task->addLink(new Link("/tasks", 'POST', 'Create a new task'));
        $task->addLink(new Link("/tasks/{$taskId}", 'PUT', 'Update title or description or both'));
        $task->addLink(new Link("/tasks/{$taskId}", 'DELETE'));
        $task->addLink(new Link("/tasks/{$taskId}/complete", 'PATCH ', 'Mark task as complete'));

        return $task;
    }
}