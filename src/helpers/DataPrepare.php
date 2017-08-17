<?php

namespace shcherbanich\bitstake\helpers;

class DataPrepare
{

    public static function process($content)
    {

        if($content) {

            $content = iconv(mb_detect_encoding($content), "UTF-8", $content);

            $content = strip_tags($content);
        }

        return $content;
    }
}