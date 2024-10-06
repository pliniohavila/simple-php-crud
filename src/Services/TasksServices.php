<?php declare(strict_types = 1);

namespace App\Services;

use App\Domain\TaskDTO;
use App\Repositories\TasksRepository;

class TasksServices 
{
    private TasksRepository $tasksRepository;

    public function __construct() 
    {
        $this->tasksRepository = new TasksRepository();
    }

    /**
     * @param TaskDTO $task DTO of the tasks
     * @return string|false
     */
    public function saveTasks(TaskDTO $task) 
    {
        $table = 'tasks';
        return $this->tasksRepository->insert($table, $task);
    }

    public function getTasks($conditions = []): array
    {
        $table = 'tasks';
        return $this->tasksRepository->getTasks($table, $conditions);
    }

    public function updateTasks($id, array $tasks)
    {
        $table = 'tasks';
        return $this->tasksRepository->update($id, $table, $tasks);
    }
}