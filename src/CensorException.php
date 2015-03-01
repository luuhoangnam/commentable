<?php

namespace Namest\Commentable;

/**
 * Class CensorException
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Namest\Commentable
 *
 */
class CensorException extends \Exception
{
    /**
     * @var string
     */
    private $word;

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string $word
     * @param string $message
     */
    public function __construct($word, $message)
    {
        $this->word = $word;

        parent::__construct($message);
    }
}
