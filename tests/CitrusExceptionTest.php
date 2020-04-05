<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusException. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Test;

use Citrus\CitrusException;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * 例外処理のテスト
 */
class CitrusExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function test_construct_特定クラスで例外発生()
    {
        $is_match_exception = false;
        try
        {
            throw new CitrusException('unit-test');
        }
        catch (CitrusException $e)
        {
            $is_match_exception = true;
        }

        $this->assertTrue($is_match_exception);
    }



    /**
     * @test
     */
    public function test_convert_特定クラスからの変換で例外発生()
    {
        $is_match_exception = false;
        try
        {
            throw CitrusException::convert(new Exception('unit-test'));
        }
        catch (CitrusException $e)
        {
            $is_match_exception = true;
        }

        $this->assertTrue($is_match_exception);
    }



    /**
     * @test
     */
    public function フックメソッドが実行される()
    {
        $count = 0;

        CitrusException::$hooks[] = function () use (&$count) {
            $count++;
        };

        try
        {
            throw new CitrusException('unit-test');
        }
        catch (CitrusException $e)
        {
        }

        $this->assertSame(1, $count);
    }



    /**
     * @test
     */
    public function exceptionIf_想定通り()
    {
        $is_match_exception = false;
        try
        {
            CitrusException::exceptionIf(function() {
                return true;
            }, 'unit-test');
        }
        catch (CitrusException $e)
        {
            $is_match_exception = true;
        }

        $this->assertTrue($is_match_exception);
    }



    /**
     * @test
     */
    public function exceptionElse_想定通り()
    {
        $is_match_exception = false;
        try
        {
            CitrusException::exceptionElse(function() {
                return false;
            }, 'unit-test');
        }
        catch (CitrusException $e)
        {
            $is_match_exception = true;
        }

        $this->assertTrue($is_match_exception);
    }
}
