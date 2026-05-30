<?php

namespace App\Empleados\Controllers;

use App\Core\Validation\Validator;
use Exception;

abstract class BaseController
{
    protected string $model = "";
    protected const RULES = [];

    // ---------------- HOOKS ----------------
    protected function beforeCreate(array &$data) {}
    protected function afterCreate($model) {}
    protected function beforeUpdate(array &$data, $model) {}
    protected function afterUpdate($model) {}
    protected function beforeDelete($model) {}

    // ---------------- CORE METHODS ----------------

    function getAll()
    {
        return ($this->model)::all();
    }

    function getOne($id)
    {
        $nombre = class_basename($this->model);
        $row = ($this->model)::find($id);

        if (empty($row)) {
            throw new Exception("$nombre $id no existe.", 1);
        }

        return $row;
    }

    function saveData(array $data)
    {
        Validator::validate($data, static::RULES);

        $this->beforeCreate($data);

        $model = new $this->model();
        $model->fill($data);
        $model->save();

        $this->afterCreate($model);

        return $model;
    }

    function modify($id, array $data)
    {
        $model = $this->getOne($id);

        Validator::validate($data, static::RULES, true);

        $this->beforeUpdate($data, $model);

        $model->fill($data);
        $model->save();

        $this->afterUpdate($model);

        return $model;
    }

    function remove($id)
    {
        $model = $this->getOne($id);

        $this->beforeDelete($model);

        $model->delete();
    }
}