<?php

namespace shcherbanich\bitstake\components\data;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\base\InvalidParamException;

/**
 * Класс для добавления параметров фильтрации
 */
class Filter
{

    private $query = null;

    private $attributes = null;

    private $strictTypes = [];

    private $allowedOperations = [];

    /**
     * Установка ActiveQuery для фильтрации
     *
     * @param ActiveQueryInterface $query
     *
     * @return Filter
     */
    public function setQuery(ActiveQueryInterface $query)
    {

        $this->query = $query;

        return $this;
    }

    /**
     * Получить установленый ActiveQuery
     *
     * @return ActiveQueryInterface|null
     */
    public function getQuery()
    {

        return $this->query;
    }

    /**
     * Установка строгих типов для аттрибутов
     *
     * @param array $values
     *
     * @return Filter
     */
    public function setStrictTypes(array $values)
    {

        $this->strictTypes = $values;

        return $this;
    }

    /**
     * Получить установленый ActiveQuery
     *
     * @return ActiveQueryInterface|null
     */
    public function getStrictTypes()
    {

        return $this->strictTypes;
    }


    /**
     * Установка нужного типа для значения
     *
     * @param string $name
     * @param mixed $value
     *
     * @return mixed $value
     */
    public function prepareValue($name, $value)
    {
        if (isset($this->strictTypes[$name])) {

            switch ($this->strictTypes[$name]) {

                case 'integer' :

                    $value *= 1;

                    break;

                case 'string' :

                    $value = $value . '';

                    break;

                case 'boolean' :

                    $value = $value ? true : false;

                    break;
            }
        }

        return $value;
    }


    /**
     * Установка доступных для фильтрации атрибутов
     *
     * @param array $attributes
     *
     * @return Filter
     */
    public function setAvailableAttributes(array $attributes = null)
    {

        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Проверка атрибута
     *
     * @param string $attribute
     *
     * @return boolean
     */
    protected function testAttribute($attribute)
    {

        if (!is_array($this->attributes) || in_array($attribute, $this->attributes)) {

            return true;
        }

        return false;
    }


    /**
     * Добавление связи с другой таблицой
     *
     * @param string $name
     * @param array $conditions
     * @param string $joinType
     * @param boolean $eagerLoading
     * @param string|null $alias
     *
     * @return Filter
     */
    public function addRelation($name, array $conditions = [], $joinType = 'LEFT JOIN', $eagerLoading = true, $alias = null)
    {
        if (!$this->query) {

            throw new InvalidParamException('Query must be set');
        }

        $this->query->joinWith([$name => function ($relationQuery) use ($conditions, $alias) {

            if ($conditions) {

                $filter = new Filter;

                $relationQuery = $filter
                    ->setQuery($relationQuery)
                    ->setConditions($conditions, $alias)
                    ->getQuery();
            }

        }], $eagerLoading, $joinType);

        return $this;
    }

    /**
     * Добавление условия выборки
     *
     * @param string $type
     * @param string $param
     * @param string $value
     * @param string|null $alias
     *
     * @return Filter
     */
    public function addCondition($type, $param, $value, $alias = null)
    {

        if (!$this->query) {

            throw new InvalidParamException('Query must be set');
        }

        try {

            if ($this->testAttribute($param) && is_string($param) && $value !== '' && $type) {

                if ($alias) {

                    $param = "{$alias}.{$param}";
                }

                switch ($type) {
                    case 'equal':
                        $this->query->andWhere(['=', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'not_equal':
                        $this->query->andWhere(['!=', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'more':
                        $this->query->andWhere(['>', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'more_eq':
                        $this->query->andWhere(['>=', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'less':
                        $this->query->andWhere(['<', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'less_eq':
                        $this->query->andWhere(['<=', $param, $this->prepareValue($param, $value)]);
                        break;

                    case 'like':
                        $this->query->andWhere(['like', "LOWER($param)", mb_strtolower($this->prepareValue($param, $value), 'UTF-8')]);
                        break;

                    case 'not like':
                        $this->query->andWhere(['not like', "LOWER($param)", mb_strtolower($this->prepareValue($param, $value), 'UTF-8')]);
                        break;

                    case 'in':
                        $val = explode(',', $value);

                        foreach ($val as &$data) {

                            $data = $this->prepareValue($param, $data);
                        }

                        unset($data);

                        $this->query->andWhere(['in', $param, $val]);
                        break;

                    case 'not_in':
                        $val = explode(',', $value);

                        foreach ($val as &$data) {

                            $data = $this->prepareValue($param, $data);
                        }

                        unset($data);

                        $this->query->andWhere(['not in', $param, $val]);
                        break;

                    case 'between':

                        $val = explode(',', $value);

                        $min = $this->prepareValue($param, $val[0]);

                        $max = isset($val[1]) ? $this->prepareValue($param, $val[1]) : $this->prepareValue($param, 0);

                        $this->query->andWhere(['between', $param, $min, $max]);

                        break;

                    case 'not_between':

                        $val = explode(',', $value);

                        $min = $this->prepareValue($param, $val[0]);

                        $max = isset($val[1]) ? $this->prepareValue($param, $val[1]) : $this->prepareValue($param, 0);

                        $this->query->andWhere(['not between', $param, $min, $max]);
                        break;

                    case 'null':
                        $this->query->andWhere([$param => null]);
                        break;

                    case 'not_null':
                        $this->query->andWhere(['not', [$param => null]]);
                        break;

                    case 'or':

                        $val = explode(',', $value);

                        $q = ['or'];

                        foreach ($val as $v) {

                            $v = $this->prepareValue($param, $v);

                            $q[] = "$param = " . is_numeric($v) ? $v : Yii::$app->db->quoteValue($v);
                        }

                        $this->query->andWhere($q);
                        break;
                }
            }
        } catch (Exception $e) {

        }

        return $this;
    }

    /**
     * Добавление условий выборки
     *
     * @param array $conditions
     * @param string|null $alias
     *
     * @return Filter
     */
    public function setConditions(array $conditions = [], $alias = null)
    {
        if (!$this->query) {

            throw new InvalidParamException('Query must be set');
        }

        foreach ($conditions as $param => $data) {

            if (is_array($data)) {

                foreach ($data as $type => $value) {

                    $this->addCondition($type, $param, $value, $alias);
                }
            }
        }

        return $this;
    }

    /**
     * Добавление группы связей
     *
     * @param array $relations
     * @param string $joinType
     * @param boolean $eagerLoading
     *
     * @return Filter
     */
    public function setRelations(array $relations = [], $joinType = 'LEFT JOIN', $eagerLoading = true)
    {
        if (!$this->query) {

            throw new InvalidParamException('Query must be set');
        }

        foreach ($relations as $name => $data) {

            $conditions = isset($data['conditions']) ? $data['conditions'] : [];

            $alias = isset($data['alias']) ? $data['alias'] : null;

            $this->addRelation($name, $conditions, $joinType, $eagerLoading, $alias);
        }

        return $this;
    }
}