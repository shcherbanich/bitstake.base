<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;

class ViewAction extends Action
{

    public $findModel;

    public function run($id)
    {
        $model = null;

        if ($this->findModel !== null) {

            $model = call_user_func($this->findModel, $id);
        }

        return $model;
    }
}
