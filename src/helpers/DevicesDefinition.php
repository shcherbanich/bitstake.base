<?php

namespace shcherbanich\bitstake\helpers;

use Mobile_Detect;
use Yii;

/**
 * Детектор устройства
 */
class DevicesDefinition
{

    /**
     * Мобильный телефон
     */
    const DEVICE_TYPE_MOBILE = 'mobile';

    /**
     * Планшет
     */
    const DEVICE_TYPE_TABLET = 'tablet';

    /**
     * PC
     */
    const DEVICE_TYPE_PC = 'pc';

    /**
     * Список доступных устройств
     *
     * @return array
     */
    public static function getDeviceTypeList()
    {

        return [
            self::DEVICE_TYPE_MOBILE => Yii::t('app', 'Мобильное устройство'),
            self::DEVICE_TYPE_TABLET => Yii::t('app', 'Планшет'),
            self::DEVICE_TYPE_PC => Yii::t('app', 'Персональный компьютер')
        ];
    }

    /**
     * @param Mobile_Detect|null $detect
     */
    private $detect;

    /**
     * Конструктор детектора устройства
     *
     * @param string $userAgent
     */
    public function __construct($userAgent)
    {

        $this->detect = new Mobile_Detect;

        $this->detect->setUserAgent($userAgent);
    }

    /**
     * Получить тип устройства
     *
     * @return string
     */
    public function detectDeviceType()
    {

        $type = null;

        if ($this->detect->isTablet()) {

            $type = self::DEVICE_TYPE_TABLET;
        } elseif ($this->detect->isMobile()) {

            $type = self::DEVICE_TYPE_MOBILE;
        } else {

            $type = self::DEVICE_TYPE_PC;
        }

        return $type;
    }

    /**
     * Получить OS устройства
     *
     * @return string
     */
    public function detectDeviceOS()
    {

        $os = null;

        if($this->detect->isMobile() || $this->detect->isTablet()) {

            $os = 'otherMobile';

            try {

                if ($this->detect->isAndroidOS()) {

                    $os = 'android';

                } elseif ($this->detect->isAndroidOS()) {

                    $os = 'ios';

                } elseif ($this->detect->isBlackBerryOS()) {

                    $os = 'blackBerry';

                } elseif ($this->detect->isSymbianOS()) {

                    $os = 'symbian';

                }
            }
            catch(\Exception $e){}

        } else{

            $os    =   "otherPC";

            try {

                $user_agent = $_SERVER['HTTP_USER_AGENT'];

                if (preg_match('/windows|win32/i', $user_agent)) {

                    $os = 'windows';

                } else if (preg_match('/macintosh|mac os x/i', $user_agent)) {

                    $os = 'osx';

                } else if (preg_match('/linux/i', $user_agent)) {

                    $os = "linux";
                }
            }
            catch(\Exception $e){}
        }

        return $os;
    }
}