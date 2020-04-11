<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusException. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus;

use Exception;
use Throwable;

/**
 * Citrusの基本例外
 */
class CitrusException extends Exception
{
    /** @var callable[] exception生成時のフック処理 */
    public static $hooks = [];



    /**
     * constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // フック処理
        foreach (self::$hooks as $hook)
        {
            $hook($this);
        }
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e
     * @return $this
     */
    public static function convert(\Exception $e): self
    {
        return new static($e->getMessage(), $e->getCode(), $e->getPrevious());
    }



    /**
     * 引数がtrueの時にexceptionがthrowされる
     *
     * @param bool|callable $expr
     * @param string        $message メッセージ
     * @return void
     * @throws $this
     */
    public static function exceptionIf($expr, string $message): void
    {
        // 無名関数の場合は再起する
        if (true === is_callable($expr))
        {
            static::exceptionIf($expr(), $message);
        }

        if (true === $expr)
        {
            throw new static($message);
        }
    }



    /**
     * 引数がfalseの時にexceptionがthrowされる
     *
     * @param bool|callable $expr
     * @param string        $message メッセージ
     * @return void
     * @throws $this
     */
    public static function exceptionElse($expr, string $message): void
    {
        // 無名関数の場合は再起する
        if (true === is_callable($expr))
        {
            static::exceptionElse($expr(), $message);
        }

        if (false === $expr)
        {
            throw new static($message);
        }
    }
}
