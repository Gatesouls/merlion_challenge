<?php
/** @author Mark Skachkov <gatesouls@gmail.com> */

class WordObject
{
    public $parentWord;
    public $value;

    /**
     * WordObject constructor.
     * @param string $parentWord
     * @param string $value
     */
    public function __construct($parentWord, $value) {
        $this->parentWord = $parentWord;
        $this->value      = $value;
    }
}