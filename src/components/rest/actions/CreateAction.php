<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class CreateAction extends Action
{
    public $scenario = 'insert';

    public $viewAction = 'view';

    public function run()
    {
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $model->loadDefaultValues()->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {

            $response = Yii::$app->getResponse();

            $response->setStatusCode(201);

            $id = implode(',', array_values($model->getPrimaryKey(true)));

            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));

        } elseif (!$model->hasErrors()) {

            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
