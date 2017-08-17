<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;
use yii\web\ServerErrorHttpException;

class DeleteAction extends Action
{

    public $findModel;

    public function run($id)
    {
        $model = null;

        if ($this->findModel !== null) {

            $model = call_user_func($this->findModel, $id);
        }

        if ($model->delete() === false) {

            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
