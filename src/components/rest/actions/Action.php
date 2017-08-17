<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;
use yii\db\ActiveRecordInterface;

class Action extends \yii\base\Action
{

    /**
     * @var ActiveRecordInterface $modelClass
     */
    public $modelClass;

    /**
     * @var array $conditions
     */
    public $conditions = [];

    /**
     * @var callable a PHP callable that will be called when running an action to determine
     * if the current user has the permission to execute the action. If not set, the access
     * check will not be performed. The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

}
