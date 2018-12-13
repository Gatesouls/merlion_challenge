<?php
/**
 * @author Mark Skachkov <gatesouls@gmail.com>
 */

class WordTransformer
{
    const RUSSIAN_ALPHABET_LENGTH = 33;
    const WORD_LENGTH = 4;

    private $_russianAlphabet;

    private $_pspellRussianDictionary;

    private $_finalWord;

    private $_usedWords;

    private $_transformationHistoryArray;

    /** array of word combinations tree */
    private $_wordsTree;

    /**
     * WordTransformer constructor.
     * @param string $firstWord
     * @param string $finalWord
     */
    public function __construct($firstWord, $finalWord) {
        $this->begin($firstWord, $finalWord);
    }

    /**
     * @param string $firstWord
     * @param string $finalWord
     */
    private function begin($firstWord, $finalWord) {
        $this->_pspellRussianDictionary = pspell_new('ru');
        $this->_russianAlphabet = $this->_getRussianAlphabetLettersArray();
        $this->_finalWord = $finalWord;
        $this->_wordsTree[] = serialize(new WordObject(null, $firstWord));
        $this->_transformWordRecursive($firstWord);
    }

    /**
     *
     * Example:
     * input: $word = 'лужа';
     * output: $lettersArray = ['л', 'у', 'ж', 'а',];
     *
     * Usage: $lettersArray[0][2] equals 'ж'
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
     * Example: $russianAlphabetLettersArray[0][3] equals 'г'
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
     * @param string $word
     */
    private function _transformWordRecursive($word) {
        $wordLettersArray = $this->_parseWordToLettersArray($word);

        for ($currentWordLetter = 0; $currentWordLetter < self::WORD_LENGTH; $currentWordLetter++) {

            for ($currentAlphabetLetter = 0; $currentAlphabetLetter < self::RUSSIAN_ALPHABET_LENGTH; $currentAlphabetLetter++) {

                if ($currentAlphabetLetter === $currentWordLetter) {
                    continue;
                }

                $wordLettersArray[0][$currentWordLetter] = $this->_russianAlphabet[0][$currentAlphabetLetter];
                $newWord = $this->_implodeLettersArrayIntoWord($wordLettersArray);

                if (!$this->_isCorrectWord($newWord)) {
                    continue;
                }

                if ($this->_isUniqueWord($newWord)) {
                    $wordObject = new WordObject($word, $newWord);
                    $wordObjectSerialized = serialize($wordObject);
                    $this->_wordsTree[] = $wordObjectSerialized;
                } else {
                    continue;
                }

                if ($newWord === $this->_finalWord) {
                    $this->_getTransformationHistoryBySerializedWord($wordObjectSerialized);
                } else {
                    $this->_transformWordRecursive($newWord);
                }

            }

        }

    }

    /**
     * @param array $lettersArray
     * @return string
     */
    private function _implodeLettersArrayIntoWord($lettersArray) {
        return implode('', $lettersArray);
    }

    /**
     * @param  string $needle
     * @param  array  $haystack
     * @param         $currentKey
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
     * @param string $word
     * @return bool
     */
    private function _isUniqueWord($word) {
        if (!$this->_arraySearchRecursive($word, $this->_usedWords)) {
            $this->_usedWords[] = $word;
            return true;
        }

        return false;
    }

    /**
     * @param string $word
     * @return bool
     */
    private function _isCorrectWord($word) {
        return pspell_check($this->_pspellRussianDictionary, $word);
    }

    /**
     * @param serialized string $word
     */
    private function _getTransformationHistoryBySerializedWord($word) {
        $word = unserialize($word);
        /** @var WordObject $word */
        $searchedWord = $word->parentWord;
        $this->_transformationHistoryArray[] = $word->value;

        while (true) {
            foreach ($this->_wordsTree as $value) {
                /** @var WordObject $wordUnserialized */
                $wordUnserialized = unserialize($value);

                if ($wordUnserialized->value === $searchedWord) {
                    $this->_transformationHistoryArray[] = $wordUnserialized->value;
                    $searchedWord = $wordUnserialized->parentWord;

                    if ($searchedWord === null) {
                        $this->end();
                    }

                }

            }
        }

    }

    /**
     * Example return:
     *
     *$this->_transformationHistoryArray[0]   = 'лужа';
     *$this->_transformationHistoryArray[1]   = 'ложа';
     * ...
     *$this->_transformationHistoryArray[...] = 'море';
     * 
     * @return array $this->_transformationHistoryArray 
     */
    private function _processTransformationHistory() {
        krsort($this->_transformationHistoryArray);

        return $this->_transformationHistoryArray;
    }

    /**
     * calls class Output which will echo the transformation history of word
     */
    private function end() {
        $TransformationHistoryArrayPublic = $this->_processTransformationHistory();
        Output::echoOutput($TransformationHistoryArrayPublic);
        die;
    }
}