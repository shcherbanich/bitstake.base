<?php

namespace shcherbanich\bitstake\components\data;

use Yii;
use yii\helpers\ArrayHelper;

class ArrayDataProvider extends \yii\data\ArrayDataProvider
{

    /**
     * @inheritdoc
     */
    protected function sortModels($models, $sort)
    {
        $orders = $sort->getOrders();

        $group = explode(',', Yii::$app->request->get('group'));

        if (count($group) > 1) {

            if ($group[0] == key($orders)) {

                $order = array_shift($orders);

                if ($order == SORT_ASC) {

                    ksort($models);

                } else {

                    krsort($models);
                }

                $models = array_values($models);
            }

            array_walk($models, function (&$item, $key) {

                if (is_array($item)) {

                    $item = array_values($item);
                }
            });

            foreach ($models as &$model) {

                ArrayHelper::multisort($model, array_keys($orders), array_values($orders));
            }

            unset($model);
        } else {

            ArrayHelper::multisort($models, array_keys($orders), array_values($orders));
        }

        return $models;
    }

}