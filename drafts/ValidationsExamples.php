<?php

// Abordagem 1: Usando uma única variável booleana
$hasInvalidTitle = !isset($data['title']) || empty($data['title']) || !is_string($data['title']);

if ($hasInvalidTitle) {
    throw ValidationException::missingField("title");
}

// Abordagem 2: Verificando cada condição separadamente para um diagnóstico mais detalhado
$titleValidation = [
    'isSet' => isset($data['title']),
    'notEmpty' => !empty($data['title']),
    'isString' => is_string($data['title'])
];

$isInvalid = !$titleValidation['isSet'] || !$titleValidation['notEmpty'] || !$titleValidation['isString'];

if ($isInvalid) {
    // Você pode até identificar qual validação específica falhou
    if (!$titleValidation['isSet']) {
        throw ValidationException::missingField("title - Field not set");
    }
    if (!$titleValidation['notEmpty']) {
        throw ValidationException::missingField("title - Field is empty");
    }
    if (!$titleValidation['isString']) {
        throw ValidationException::missingField("title - Field is not a string");
    }
}

// Abordagem 3: Usando uma função de validação reutilizável
function validateField(array $data, string $fieldName, array $rules = ['required', 'notEmpty', 'string']): array
{
    $validation = [
        'isValid' => true,
        'errors' => []
    ];

    if (in_array('required', $rules) && !isset($data[$fieldName])) {
        $validation['isValid'] = false;
        $validation['errors'][] = "$fieldName is required";
    }

    if (in_array('notEmpty', $rules) && empty($data[$fieldName])) {
        $validation['isValid'] = false;
        $validation['errors'][] = "$fieldName cannot be empty";
    }

    if (in_array('string', $rules) && !is_string($data[$fieldName])) {
        $validation['isValid'] = false;
        $validation['errors'][] = "$fieldName must be a string";
    }

    return $validation;
}

// Exemplo de uso da função de validação
$titleValidationResult = validateField($data, 'title');

if (!$titleValidationResult['isValid']) {
    throw ValidationException::missingField(implode(", ", $titleValidationResult['errors']));
}