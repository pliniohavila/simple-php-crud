<?php declare(strict_types = 1);

namespace App\Traits;

trait HttpResponse 
{
    public function jsonResponse(int $statusCode, array $data)
    {
        header('Content-Type: application/json', true, $statusCode);
        echo json_encode($data);
        exit;
    }

    public function errorResponse(int $statusCode, string $title, string $detail)
    {
        $error = [
            'type'   => 'http://localhost/tasks/' . strtolower(str_replace(' ', '-', $title)),
            'title'  => $title,
            'status' => $statusCode,
            'detail' => $detail,
        ];

        return $this->jsonResponse($statusCode, $error);
    }
}