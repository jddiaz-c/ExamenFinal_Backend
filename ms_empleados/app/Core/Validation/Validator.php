<?php

namespace App\Core\Validation;

use Exception;

class Validator
{
    public static function validate(array $data, array $rules, bool $isUpdate = false): void
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {

            $value = $data[$field] ?? null;
            $isEmpty = is_null($value) || $value === '';

            // Si es update y el campo no viene, se omite
            if ($isUpdate && $isEmpty) {
                continue;
            }

            // Required
            if (!empty($fieldRules['required']) && $isEmpty) {
                $errors[] = "El campo '$field' es obligatorio.";
                continue;
            }

            if ($isEmpty) {
                continue;
            }

            // Type
            if (isset($fieldRules['type'])) {
                switch ($fieldRules['type']) {
                    case 'string':
                        if (!is_string($value)) {
                            $errors[] = "El campo '$field' debe ser texto.";
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[] = "El campo '$field' debe ser un correo válido.";
                        }
                        break;

                    case 'date':
                        $d = \DateTime::createFromFormat('Y-m-d', $value);
                        if (!$d || $d->format('Y-m-d') !== $value) {
                            $errors[] = "El campo '$field' debe tener formato YYYY-MM-DD.";
                        }
                        break;

                    case 'integer':
                        if (!is_numeric($value) || intval($value) != $value) {
                            $errors[] = "El campo '$field' debe ser un número entero.";
                        }
                        break;
                }
            }

            // Min length
            if (isset($fieldRules['min']) && strlen($value) < $fieldRules['min']) {
                $errors[] = "El campo '$field' debe tener mínimo {$fieldRules['min']} caracteres.";
            }

            // Max length
            if (isset($fieldRules['max']) && strlen($value) > $fieldRules['max']) {
                $errors[] = "El campo '$field' no puede superar {$fieldRules['max']} caracteres.";
            }

            // Regex
            if (isset($fieldRules['regex']) && !preg_match($fieldRules['regex'], $value)) {
                $errors[] = "El campo '$field' tiene un formato inválido.";
            }

            // Enum
            if (isset($fieldRules['enum']) && !in_array($value, $fieldRules['enum'])) {
                $allowed = implode(', ', $fieldRules['enum']);
                $errors[] = "El campo '$field' debe ser uno de: $allowed.";
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode(' | ', $errors), 2);
        }
    }
}