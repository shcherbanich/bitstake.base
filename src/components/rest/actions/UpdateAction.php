<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;
use yii\web\ServerErrorHttpException;


class UpdateAction extends Action
{

    public $scenario = 'update';

    public $search = [];

    public $findModel;

    public function run($id)
    {

        $model = null;

        if ($this->findModel !== null) {

            $model = call_user_func($this->findModel, $id);
        }

        $model->scenario = $this->scenario;

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save() === false && !$model->hasErrors()) {

            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }

}
