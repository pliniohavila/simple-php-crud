<?php declare(strict_types = 1);

namespace App\Domain;
use \Datetime;

/**
 * Class TaskDTO
 * 
 * Esta classe é um Data Transfer Object (DTO) que encapsula os dados de uma tarefa.
 */
class TaskDTO 
{
    /**
     * @var int|null $id O identificador único da tarefa. Pode ser nulo ao criar uma nova tarefa.
     */
    public ?int $id;

    /**
     * @var string $title O título da tarefa. Este campo é obrigatório.
     */
    public string $title;

    /**
     * @var string $description A descrição detalhada da tarefa. Este campo é obrigatório.
     */
    public string $description;

    /**
     * @var DateTime $createdAt A data e hora de criação da tarefa. Este campo é obrigatório.
     */
    public DateTime $createdAt;

    /**
     * @var DateTime|null $updatedAt A data e hora da última atualização da tarefa. Pode ser nula se a tarefa ainda não foi atualizada.
     */
    public ?DateTime $updatedAt;

    /**
     * @var DateTime|null $completedAt A data e hora de conclusão da tarefa. Pode ser nula se a tarefa ainda não foi concluída.
     */
    public ?DateTime $completedAt;

    public array $links = [];

    /**
     * TaskDTO constructor.
     *
     * @param int|null $id O identificador único da tarefa, pode ser nulo na criação de uma nova tarefa.
     * @param string $title O título da tarefa.
     * @param string $description A descrição da tarefa.
     * @param DateTime $createdAt A data e hora de criação da tarefa.
     * @param DateTime|null $updatedAt A data e hora da última atualização, opcional.
     * @param DateTime|null $completedAt A data e hora de conclusão da tarefa, opcional.
     */
    public function __construct(
        ?int $id = null,
        string $title,
        string $description,
        DateTime $createdAt,
        ?DateTime $updatedAt = null,
        ?DateTime $completedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->completedAt = $completedAt;
    }

    public function addLink(Link $link): void {
        $this->links[] = $link;
    }
}
