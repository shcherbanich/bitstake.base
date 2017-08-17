<?php

namespace shcherbanich\bitstake\helpers\microService;

interface RequestInterface {

    /**
     * Установка команды
     *
     * @param string $name
     *
     * @return void
     */
    public function setCommand($name);

    /**
     * Получение установленной в данный момент команды
     *
     * @return string $command
     */
    public function getCommand();

    /**
     * Добавить параметр
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function addParam($key, $value);

    /**
     * Установить параметры запроса
     *
     * @param array $params
     *
     * @return void
     */
    public function setParams(array $params = []);

    /**
     * Получить параметры запроса
     *
     * @return array $params
     */
    public function getParams();

    /**
     * Прикрепить файл
     *
     * @param string $name
     * @param string $patch
     *
     * @return void
     */
    public function addFile($name, $patch);

    /**
     * Получить прикрепленные файлы
     *
     * @return array $files
     */
    public function getFiles();
}
