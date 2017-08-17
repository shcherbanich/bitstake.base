<?php

namespace shcherbanich\bitstake\helpers\microService;

/**
 * Оболочка для работы с микросервисом
 */
interface ServiceInterface {

    /**
     * Отправить запрос к микросервису
     *
     * @param string $key
     *
     * @param callable $handler обработчик ( запрос передается по ссылке )
     *
     * @return void
     */
    public function addRequestHandler($key, callable $handler = null);

    /**
     * Отправить запрос к микросервису
     *
     * @param RequestInterface $request
     *
     * @param array $options дополнительные параметры
     *
     * @return ResponseInterface $response
     */
    public function sendRequest(RequestInterface $request, array $options = []);
}
