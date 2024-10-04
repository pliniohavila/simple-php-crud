<?php declare(strict_types = 1);

namespace App\Repositories;

use App\Domain\TaskDTO;
use App\Core\Database;


class TasksRepository
{
    protected $pdo;

    public function __construct() {
        $this->pdo = Database::connection();
    }

    /**
     * @param string $table The name of the table.
     * @param array $conditions Conditions to the where clause
     * @return array
     */
    public function getTasks(string $table, array $conditions = []): array 
    {
        $query = "select * from {$table}";
        $conditions = array_filter($conditions);

        if (!empty($conditions)) 
            $query .= $this->processConditions($conditions);

        $statement = $this->pdo->prepare($query);
        $statement->execute($conditions);

        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $tasks = array_map(function ($taskData) {
            return new TaskDTO(
                $taskData['id'] ?? null,
                $taskData['title'],
                $taskData['description'],
                new \DateTime($taskData['created_at']),
                isset($taskData['updated_at']) ? new \DateTime($taskData['updated_at']) : null,
                isset($taskData['completed_at']) ? new \DateTime($taskData['completed_at']) : null
            );
        }, $results);

        return $tasks;
    }

    /**
     * @param string $table The name of the table.
     * @param TaskDTO $data DTO of the tasks to be save
     * @return string|false
     */
    public function insert(string $table, TaskDTO $data) 
    {
        $table = "public.$table";
        $tasksArray = array_filter(get_object_vars($data));
        $tasksArray['createdAt'] = ((array) $tasksArray['createdAt'])['date'];
        
        $keys = $this->prepareColumName(implode(', ', array_keys($tasksArray)));

        $query = "INSERT INTO {$table} ({$keys})";
        $query .= $this->processInsertedData($tasksArray);

        $statement = $this->pdo->prepare($query);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function updateTasks($id, string $table, array $tasks)
    {
        $task = $this->getTasks($table, ['id' => $id])[0];
        if (is_null($tasks))
    }

    private function processConditions($conditions): string
    {
        $attributes = array_keys($conditions);
        $sql = implode(" AND ", array_map(fn ($attr) => "$attr = :$attr", $attributes));

        return " WHERE {$sql}";
    }
    

    private function processInsertedData($data): string 
    {
        $attributes = array_keys($data);
        $sql = implode(", ", array_map(fn ($attr) => "'{$data[$attr]}'", $attributes));

        return " VALUES({$sql})";
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
}