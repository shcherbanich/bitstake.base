<?php

namespace shcherbanich\bitstake\helpers\microService;

/**
 * @inheritdoc
 */
class Response implements ResponseInterface {

    private $content;

    private $status = self::STATUS_FAIL;


    public function __construct(array $data = []){

        if(isset($data['status'])){

            $this->setStatus($data['status']);
        }

        if(isset($data['content'])){

            $this->setContent($data['content']);
        }
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status){

        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function setContent($content){

        $this->content = $content;
    }

    /**
     * @inheritdoc
     */
    public function getContent(){

        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function getStatus(){

        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function getData($type = self::FORMAT_JSON){

        $data = null;

        switch($type){

            case self::FORMAT_JSON:
                $data = json_decode($this->getContent() ,true);
                break;
            case self::FORMAT_TEXT:
                $data = $this->getContent();
                break;

        }

        return $data;
    }
}
