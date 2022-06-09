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
    protected static array $hooks = [];

    /** @var string 内部メッセージ */
    protected string $internal_message;



    /**
     * constructor.
     *
     * @param string         $message  メッセージ
     * @param int            $code     エラーコード
     * @param Throwable|null $previous 以前のスローされた例外
     */
    public function __construct(string $message = '', int $code = 0, Throwable|null $previous = null)
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
     * @return string 内部メッセージ
     */
    public function getInternalMessage(): string
    {
        return $this->internal_message;
    }



    /**
     * @param string $internal_message 内部メッセージ
     */
    public function setInternalMessage(string $internal_message): void
    {
        $this->internal_message = $internal_message;
    }



    /**
     * 生成時フックの追加
     *
     * @param callable $hook フック用のcallableな関数
     */
    public static function addHook(callable $hook): void
    {
        self::$hooks[] = $hook;
    }



    /**
     * CitrusException converter
     *
     * @param Exception $e 例外
     * @return $this
     */
    public static function convert(\Exception $e): self
    {
        return new static($e->getMessage(), $e->getCode(), $e->getPrevious());
    }



    /**
     * 引数がtrueの時にexceptionがthrowされる
     *
     * @param callable|bool $expr    exception条件の無名関数
     * @param string        $message メッセージ
     * @throws $this
     */
    public static function exceptionIf(callable|bool $expr, string $message): void
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
     * @param callable|bool $expr    exception条件の無名関数
     * @param string        $message メッセージ
     * @throws $this
     */
    public static function exceptionElse(callable|bool $expr, string $message): void
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
