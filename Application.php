<?php
/**
 * @author Mark Skachkov <gatesouls@gmail.com>
 */

class Application
{
    const RUSSIAN_ALPHABET_LENGTH = 33;
    const WORD_LENGTH = 4;

    private $_russianAlphabet;

    private $_finalWord;

    /**
     * array of word combinations tree
     */
    private $_wordsTree;


    public function transform($firstWord, $finalWord) {
        $this->_russianAlphabet = $this->_getRussianAlphabetLettersArray();
        $this->_finalWord = $finalWord;
        $this->_findSimilarWordsRecursive($firstWord);

        /**
         * @todo Needs work
         */

    }

    /**
     *
     * Example:
     * input: $word = 'лужа';
     * output: $lettersArray = ['л', 'у', 'ж', 'а',];
     *
     * @param string $word
     * @return array
     */
    private function _parseWordToLettersArray($word) {
        $lettersArray = [];
        preg_match_all('#.{1}#uis', $word, $lettersArray);
        return $lettersArray;
    }

    /**
     *
     * Usage: $russianAlphabetLettersArray[0][0] is equal to 'a'
     *
     * @return array
     */
    private function _getRussianAlphabetLettersArray() {
        $russianAlphabet = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
        $russianAlphabetLettersArray = [];
        preg_match_all('#.{1}#uis', $russianAlphabet, $russianAlphabetLettersArray);
        return $russianAlphabetLettersArray;
    }

    /**
     * @todo Needs work
     * @param $word
     */
    private function _findSimilarWordsRecursive($word) {
        $wordLettersArray = $this->_parseWordToLettersArray($word);

        for ($currentWordLetter = 0; $currentWordLetter < self::WORD_LENGTH; $currentWordLetter++) {

            for ($currentAlphabetLetter = 0; $currentAlphabetLetter < self::RUSSIAN_ALPHABET_LENGTH; $currentAlphabetLetter++) {

                if ($currentAlphabetLetter === $currentWordLetter) {
                    continue;
                }

                $newWord = ($wordLettersArray[0][$currentWordLetter] = $this->_russianAlphabet[0][$currentAlphabetLetter]);
                $newWord = $this->_implodeLettersArrayIntoWord($newWord);



                /**
                 * @todo implement validation of $newWord by searching in dictionary
                 */

                /**
                 * @todo implement saving of words and their paths as array indexes (e.g. $this->_wordTree[лужа][ложа][кожа][...][море]) inside @this->_wordTree (attempt is presented below)
                 */
                $parentWordIndex = $this->getWordindex($word);

                if ($parentWordIndex !== false && $this->_isUniqueWord($newWord)) {
                    /**
                     * @deprecated $this->_wordsTree{$parentWordIndex}[$newWord] = $newWord does not work
                     */
                    $this->_wordsTree{$parentWordIndex}[$newWord] = $newWord;
                } else {
                    continue;
                }

                if ($newWord === $this->_finalWord) {
                    /**
                     * @todo Implement end of the script
                     */
                } else {
                    $this->_findSimilarWordsRecursive($newWord);
                }

            }

        }

    }

    /**
     * @param $lettersArray
     * @return string
     */
    private function _implodeLettersArrayIntoWord($lettersArray) {
        return implode('', $lettersArray);
    }

    /**
     * @deprecated $this->_arraySearchRecursive() returns index only if $word is not an array
     * @todo Needs Work
     * @param $word
     * @return int|string|bool
     */
    private function getWordindex($word) {
        return $this->_arraySearchRecursive($word, $this->_wordsTree);
    }

    /**
     * @param  string $needle
     * @param  array  $haystack
     * @param  string $currentKey
     * @return        bool|string
     */
    private function _arraySearchRecursive($needle, $haystack, $currentKey = '') {
        foreach($haystack as $key=>$value) {
            if (is_array($value)) {
                $nextKey = $this->_arraySearchRecursive($needle,$value, $currentKey . '[' . $key . ']');
                if ($nextKey) {
                    return $nextKey;
                }

            } elseif ($value==$needle) {
                return is_numeric($key) ? $currentKey . '[' .$key . ']' : $currentKey . '[' .$key . ']';
            }
        }
        return false;
    }

    /**
     * @param string|int $key
     * @param array      $array
     * @return bool
     */
    private function _arrayKeyExistsRecursive($key, $array) {
        if (array_key_exists($key, $array)) {
            return true;
        } else {
            foreach ($array as $value) {
                if (is_array($value)) {
                    if ($this->_arrayKeyExistsRecursive($key, $value)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param string $word
     * @return bool
     */
    private function _isUniqueWord($word) {
        if ($this->_arrayKeyExistsRecursive($word, $this->_wordsTree)) {
            return false;
        } else {
            return true;
        }
    }
}