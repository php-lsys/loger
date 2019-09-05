<?php
namespace LSYS\Loger;
/**
 * @method \LSYS\Loger loger()
 */
class DI extends \LSYS\DI{
    /**
     * @return static
     */
    public static function get(){
        $di=parent::get();
        !isset($di->loger)&&$di->loger(new \LSYS\DI\SingletonCallback(function (){
            return new \LSYS\Loger();
        }));
        return $di;
    }
}