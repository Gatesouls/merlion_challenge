<?php
/** @author Mark Skachkov <gatesouls@gmail.com> */

const MAX_WORD_LENGTH = 4;

if ($argc !== 2) {
    echo 'Ошибка: Неправильное количество параметров. Пример: лужа море';
    die;
}

$firstWord  = $argv[1];
$secondWord = $argv[2];

if (!is_string($firstWord) || !is_string($secondWord)) {
    echo "Ошибка: допустимы только словарные слова в именительном падеже единственного числа";
    die;
}
if (strlen($firstWord) != MAX_WORD_LENGTH || strlen($secondWord) != MAX_WORD_LENGTH) {
    echo "Ошибка: количество символов в каждом слове должно равняться" . MAX_WORD_LENGTH;
    die;
}

$wordTransformer = new WordTransformer($firstWord, $secondWord);
