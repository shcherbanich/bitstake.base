<?php

namespace shcherbanich\bitstake\components\rest\actions;

use Yii;

class IndexAction extends Action
{

    public $prepareDataProvider;

    public $pageSize = 10;

    public $conditions = [];

    public $findModel;

    public function run()
    {

        if ($this->prepareDataProvider !== null) {

            return call_user_func($this->prepareDataProvider);
        }

        return null;
    }
}
