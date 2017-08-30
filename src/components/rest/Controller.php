<?php

namespace shcherbanich\bitstake\components\rest;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecordInterface;
use yii\web\Response;
use yii\filters\Cors;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\data\Pagination;
use shcherbanich\bitstake\components\data\Filter;
use yii\web\NotFoundHttpException;

class Controller extends \yii\rest\Controller
{
    public $serializer = 'shcherbanich\bitstake\components\data\rest\Serializer';

    public $modelClass = null;

    public $conditions = [];

    protected $pageSize = 10;

    public function init()
    {
        parent::init();

        Yii::$app->user->enableSession = false;

        $pageSize = Yii::$app->request->get('per-page') * 1;

        $this->pageSize = $pageSize ? $pageSize : $this->pageSize;

        $headers = Yii::$app->response->headers;

        $headers->add('Access-Control-Allow-Origin', '*');

        $headers->add('Access-Control-Allow-Credentials', 'true');
    }

    public function prepareQuery($query = null){

        if ($query) {

            $modelClass = new $query->modelClass;
        } else {

            $modelClass = new $this->modelClass;

            $query = $modelClass::find();
        }

        $conditions = [];

        if (!Yii::$app->request->get('with_deleted')) {

            $conditions = [
                'deleted_at' => [
                    'null' => 1
                ]
            ];
        }

        $fields = $modelClass->fields();

        $q = Yii::$app->request->get('q');

        if (is_array($q)) {

            foreach ($q as $field => $condition) {

                if (isset($fields[$field]) && is_string($fields[$field])) {

                    $conditions[$fields[$field]] = $condition;
                } else {

                    $conditions[$field] = $condition;
                }

            }
        }

        $filter = new Filter;

        $attributes = $modelClass->attributes();

        $tableName = $modelClass::tableName();

        $ids = array_keys($modelClass->getPrimaryKey(true));

        array_walk($ids, function (&$item) use ($tableName) {

            $item = "{$tableName}.{$item}";
        });

        return $filter
            ->setQuery($query)
            ->setConditions($this->conditions, $tableName)
            ->setAvailableAttributes($fields ? $fields : $attributes)
            ->setConditions($conditions, $tableName)
            ->getQuery();
    }

    public function prepareDataProvider($query = null, $prepareQuery = true)
    {
        if (Yii::$app->request->isPost) {

            return $this->runAction('create');
        }

        if ($query) {

            $modelClass = new $query->modelClass;
        } else {

            $modelClass = new $this->modelClass;

            $query = $modelClass::find();
        }

        if($prepareQuery) {

            $query = $this->prepareQuery($query);
        }

        $count = $query->count();

        $fields = [];

        foreach ($modelClass->fields() as $field) {

            if (is_string($field)) {

                $fields[] = $field;
            }
        }

        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => new Pagination([
                'pageSize' => $this->pageSize
            ]),
            'sort' => new Sort([
                'attributes' => $fields
            ])
        ]);

        $data->setTotalCount($count);

        return $data;
    }

    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @param ActiveQuery $query
     * @return ActiveRecordInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id, $query = null)
    {

        if ($query) {

            $modelClass = new $query->modelClass;
        } else {

            $modelClass = new $this->modelClass;

            $query = $modelClass::find();
        }

        try {

            $keys = $modelClass::primaryKey();

            $filter = new Filter();

            $modelClass = new $modelClass;

            $fields = $modelClass->fields();

            $attributes = $modelClass->attributes();

            $query = $filter
                ->setQuery($query)
                ->setConditions($this->conditions, $modelClass::tableName())
                ->setAvailableAttributes($fields ? $fields : $attributes)
                ->getQuery();

            if (count($keys) > 1) {

                $values = explode(',', $id);

                if (count($keys) === count($values)) {

                    foreach ($keys as $k => $key) {

                        $query->andWhere(["{$modelClass::tableName()}.{$key}" => $values[$k]]);
                    }

                    $model = $query->one();
                }

            } elseif ($id) {

                $key = current($keys);

                $model = $query
                    ->andWhere(["{$modelClass::tableName()}.{$key}" => $id])
                    ->one();
            }
        }
        catch(\Exception $e){}

        if (!isset($model)) {

            throw new NotFoundHttpException("Object not found: $id");
        }

        return $model;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'text/html' => Response::FORMAT_JSON,
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Allow-Headers' => ['*'],
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Per-Page',
                    'X-Pagination-Total-Count',
                    'Link',
                    'Date'
                ]
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\IndexAction',
            'modelClass' => $this->modelClass,
            'conditions' => $this->conditions,
            'prepareDataProvider' => [$this, 'prepareDataProvider'],
            'checkAccess' => [$this, 'checkAccess'],
            'pageSize' => $this->pageSize
        ];

        $actions['create'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\CreateAction',
            'modelClass' => $this->modelClass,
            'conditions' => $this->conditions,
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => 'insert'
        ];

        $actions['update'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\UpdateAction',
            'modelClass' => $this->modelClass,
            'conditions' => $this->conditions,
            'findModel' => [$this, 'findModel'],
            'checkAccess' => [$this, 'checkAccess'],
            'scenario' => 'update'
        ];

        $actions['view'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\ViewAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'findModel' => [$this, 'findModel'],
            'conditions' => $this->conditions,
        ];

        $actions['delete'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\DeleteAction',
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
            'findModel' => [$this, 'findModel'],
            'conditions' => $this->conditions,
        ];

        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction'
        ];

        $actions['delete-all'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\DeleteAllAction',
            'modelClass' => $this->modelClass,
            'prepareQuery' => [$this, 'prepareQuery'],
            'checkAccess' => [$this, 'checkAccess']
        ];

        $actions['update-all'] = [
            'class' => 'shcherbanich\bitstake\components\rest\actions\UpdateAllAction',
            'modelClass' => $this->modelClass,
            'serializer' => $this->serializer,
            'prepareDataProvider' => [$this, 'prepareDataProvider'],
            'prepareQuery' => [$this, 'prepareQuery'],
            'checkAccess' => [$this, 'checkAccess']
        ];

        return $actions;
    }

    public function beforeAction($action)
    {
        $beforeAction = parent::beforeAction($action);

        if (Yii::$app->request->isOptions && $action->id != 'options') {

            $this->runAction('options');

            return false;
        }

        return $beforeAction;
    }

    protected function createScopeString(array $scopes = [])
    {
        $scope = null;

        if (isset($scopes[$this->action->id]) && is_array($scopes[$this->action->id])) {

            $scope = implode(' ', $scopes[$this->action->id]);
        }

        return $scope;
    }


}