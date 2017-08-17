<?php

namespace shcherbanich\bitstake\helpers;

class Generator
{
    public static function convertIntToShortCode($id)
    {

        $chars = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ_";

        $chars = preg_split('//', $chars, -1, PREG_SPLIT_NO_EMPTY);

        $id = intval($id);

        $length = count($chars);

        $code = "";
        while ($id > $length - 1) {
            $num = (integer)fmod($id, $length);
            $code = $chars[$num] . $code;
            $id = floor($id / $length);
        }
        $code = $chars[$id] . $code;

        return $code;
    }

    public static function uniqueId($random_pseudo = 0)
    {
        $hex = '';

        if ($random_pseudo) {

            $bytes = openssl_random_pseudo_bytes($random_pseudo, $cstrong);

            $hex = bin2hex($bytes);
        }

        return $hex . self::convertIntToShortCode(hexdec(uniqid(mt_rand(0, 10))) . mt_rand(0, 99));
    }

    /**
     * Генерация псевдослучайного числа опреденной длины
     *
     * @param integer $length
     *
     * @return integer
     */
    public static function rand($length = 1){

        $min = pow(10, $length - 1);

        $max = pow(10, $length) - 1;

        return mt_rand($min, $max);
    }

    public static function md5HexToDec($hex_str)
    {
        $dec = [];

        $arr = str_split($hex_str, 4);

        foreach ($arr as $grp) {

            $dec[] = str_pad(hexdec($grp), 5, '0', STR_PAD_LEFT);
        }

        return implode('', $dec);
    }

    public static function md5DecToHex($dec_str)
    {
        $hex = [];

        $arr = str_split($dec_str, 5);

        foreach ($arr as $grp) {

            $hex[] = str_pad(dechex($grp), 4, '0', STR_PAD_LEFT);
        }
        return implode('', $hex);
    }
}