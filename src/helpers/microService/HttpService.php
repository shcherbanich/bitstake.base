<?php

namespace shcherbanich\bitstake\helpers\microService;

use yii\httpclient\Client;

/**
 * @inheritdoc
 */
class HttpService implements ServiceInterface {

    /**
     * @var string $baseUrl базовый URL
     */
    private $baseUrl;

    /**
     * @var callable[] $requestHandler обработчики запросов перед отправкой
     */
    private $requestHandlers = [];

    /**
     * Установка базового URL
     *
     * @param string $baseUrl
     *
     * @return void
     */
    public function setBaseUrl($baseUrl){

        $this->baseUrl = $baseUrl;
    }

    /**
     * Получить установленный базовый URL
     *
     * @return string $baseUrl
     */
    public function getBaseUrl(){

        return $this->baseUrl;
    }

    /**
     * @inheritdoc
     */
    public function addRequestHandler($key, callable $handler = null){

        if($handler){

            $this->requestHandlers[$key] = $handler;
        }
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request, array $options = []){

        $method = isset($options['method']) ? $options['method'] : 'get';

        $client = new Client([
            //'transport' => 'yii\httpclient\CurlTransport',
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'baseUrl' => $this->getBaseUrl()
        ]);

        $httpRequest = $client->createRequest();

        $httpRequest->setMethod($method);

        $httpRequest->setData($request->getParams());

        $httpRequest->url = $request->getCommand();

        $files = $request->getFiles();

        foreach($files as $name => $patch) {

            $httpRequest->addFile($name, $patch);
        }

        if($this->requestHandlers){

            foreach($this->requestHandlers as $handler){

                $handler($httpRequest);
            }
        }

        $httpResponse = $httpRequest->send();

        return new Response([
            'status' => in_array($httpResponse->getStatusCode(), [200, 201]) ? Response::STATUS_SUCCESS : Response::STATUS_FAIL,
            'content' => $httpResponse->getContent()
        ]);
    }
}
