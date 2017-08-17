<?php

namespace shcherbanich\bitstake\helpers\microService;

interface ResponseInterface {

    const STATUS_SUCCESS = 1;

    const STATUS_FAIL = 2;

    const FORMAT_TEXT = 'text';

    const FORMAT_JSON = 'json';

    /**
     * Установить статус ответа
     *
     * @param string $status
     *
     * @return void
     */
    public function setStatus($status);

    /**
     * Установить контент ответа
     *
     * @param string $content
     *
     * @return void
     */
    public function setContent($content);

    /**
     * Получить статус ответа
     *
     * @return string $status
     */
    public function getStatus();

    /**
     * Распарсить данные ответа
     *
     * @param string $type
     *
     * @return mixed $data
     */
    public function getData($type = self::FORMAT_TEXT);
}
