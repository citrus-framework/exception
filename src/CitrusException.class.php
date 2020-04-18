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
 *
 * throwsの型チェックが厳しいので継承する場合は再定義推奨
 */
class CitrusException extends Exception
{
    /** @var callable[] exception生成時のフック処理 */
    private static $hooks = [];

    /** @var string 内部メッセージ */
    private $internal_message;



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

        // 通常は内部メッセージにもコピー
        $this->setInternalMessage($message);

        // フック処理
        foreach (self::$hooks as $hook)
        {
            $hook($this);
        }
    }



    /**
     * @return string
     */
    public function getInternalMessage(): string
    {
        return $this->internal_message;
    }



    /**
     * @param string $internal_message
     */
    public function setInternalMessage(string $internal_message): void
    {
        $this->internal_message = $internal_message;
    }



    /**
     * 生成時フックの追加
     *
     * @param callable $hook
     */
    public static function addHook(callable $hook)
    {
        self::$hooks[] = $hook;
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e
     * @return static
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
