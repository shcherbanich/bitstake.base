<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;

class DeleteAllAction extends Action
{

    public $modelClass;

    public $prepareQuery;

    public function run()
    {

        $modelClass = $this->modelClass;

        $query = null;

        if ($this->prepareQuery !== null) {

            $query = call_user_func($this->prepareQuery);
        }

        if($query) {

            $modelClass::deleteAll($query->where);
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
