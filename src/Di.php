<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\di;

/**
 * Singleton/Container for Di
 *
 * @author mendel
 */
class Di
{
    private static $instance = null;

    public static function setInstance(?DiInterface $intance) : void
    {
        self::$instance = $intance;
    }

    public static function getInstance() : DiInterface
    {
        if(is_null(self::$instance)) {
            self::$instance = new Container();
        }
        return self::$instance;
    }
}
