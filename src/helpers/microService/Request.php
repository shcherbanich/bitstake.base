<?php

namespace shcherbanich\bitstake\helpers\microService;

/**
 * Запрос к микросервису
 */
class Request implements RequestInterface {

    /**
     * @var array $command команда
     */
    private $command;

    /**
     * @var array $params параметры запроса
     */
    private $params = [];

    /**
     * @var array $files прикрепленные файлы
     */
    private $files = [];

    /**
     * @inheritdoc
     */
    public function setCommand($name){

        $this->command = $name;
    }

    /**
     * @inheritdoc
     */
    public function getCommand(){

        return $this->command;
    }

    /**
     * @inheritdoc
     */
    public function addParam($key, $value){

        $this->params[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setParams(array $params = []){

        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function getParams(){

        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function addFile($name, $patch){

        $this->files[$name] = $patch;
    }

    /**
     * @inheritdoc
     */
    public function getFiles(){

        return $this->files;
    }
}
