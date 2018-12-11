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

    public function __construct()
    {
        $this->_russianAlphabet = $this->_getRussianAlphabetLettersArray();
}

    public function transform($firstWord, $finalWord) {
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

                $newWord = $wordLettersArray[0][$currentWordLetter] = $this->_russianAlphabet[0][$currentAlphabetLetter];



                /**
                 * @todo implement validation of $newWord by searching in dictionary
                 */

                /**
                 * @todo implement saving of words and their paths (e.g. $this->_wordTree[лужа][ложа][кожа][...][море]) inside @this->_wordTree (attempt is presented below)
                 */
                $parentWordPath = $this->getWordPath($word);
                if ($parentWordPath !== false) {
                    $this->_wordsTree[$parentWordPath][$newWord];
                } else {
                    continue;
                }

                if ($newWord === $this->_finalWord) {
                    /**
                     * @todo Implement end of the script
                     */
                }

            }

        }

    }

    /**
     * @deprecated This function is useless and will be removed
     * @param $lettersArray
     * @return string
     */
    private function _implodeLettersArrayIntoWord($lettersArray) {
        return implode('', $lettersArray);
    }

    /**
     * @deprecated this function does not work because array_search() cannot process multi-dimensional arrays
     * @param $word
     * @return int|string
     */
    private function getWordPath($word) {
        return array_search($word, $this->_wordsTree);
    }
}