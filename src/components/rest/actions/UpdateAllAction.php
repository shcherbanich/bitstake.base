<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;
use yii\web\ServerErrorHttpException;


class UpdateAllAction extends Action
{

    public $prepareDataProvider;

    public $scenario = 'group-update';

    public $modelClass;

    public $serializer;

    public $prepareQuery;

    public function run()
    {
        $model = null;

        $query = null;

        if ($this->prepareQuery !== null) {

            $query = call_user_func($this->prepareQuery);

            $model = $query->one();
        }

        if ($model) {

            $model->setScenario($this->scenario);

            $params = Yii::$app->getRequest()->getBodyParams();

            if($params) {

                $model->load($params, '');

                if ($model->validate(array_keys($params))) {

                    $activeAttributes = $model->activeAttributes();

                    $update = [];

                    $time = time();

                    $writeUpdateAt = $model->hasAttribute('updated_at');

                    foreach ($activeAttributes as $activeAttribute) {

                        if (isset($params[$activeAttribute])) {

                            $update[$activeAttribute] = $model->{$activeAttribute};

                            if ($writeUpdateAt) {

                                $update['updated_at'] = $time;
                            }
                        }
                    }

                    if ($update) {

                        if (!$model::updateAll($update, $query->where)) {

                            throw new ServerErrorHttpException('Failed to update the objects for unknown reason.');
                        }
                    }

                } else {

                    return $model;
                }
            }
        }

        if ($this->prepareDataProvider !== null) {

            Yii::$app->getResponse()->setStatusCode(200);

            return call_user_func_array($this->prepareDataProvider, [$query, false]);
        }

        Yii::$app->getResponse()->setStatusCode(204);

        return null;
    }
}
