<?php

namespace shcherbanich\bitstake\components\data\rest;

class StatisticSerializer extends \yii\rest\Serializer
{

    /**
     * Serializes a set of models.
     * @param array $models
     * @return array the array representation of the models
     */
    protected function serializeModels(array $models)
    {
        list ($fields, $expand) = $this->getRequestedFields();

        foreach ($models as $i => $model) {

            if ($fields) {

                $filterModel = [];

                foreach ($model as $field => $value) {

                    if (in_array($field, $fields)) {

                        $filterModel[$field] = $value;
                    }
                }

                $models[$i] = $filterModel;
            }
        }
        return $models;
    }
}