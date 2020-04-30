<?php
/*
 * @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 * @license see license.txt
 */

namespace drycart\di\tests\dummy;

/**
 * Description of Dummy3
 *
 * @author mendel
 */
class DummyServiceProvider implements DummyInterface
{
    public function __construct(int $answer)
    {
        if(!defined('DI_REQUIRED_TEST_CONST')) {
            define('DI_REQUIRED_TEST_CONST', $answer);
        }
    }
}
