<?php
/** @author Mark Skachkov <gatesouls@gmail.com> */

class Output {
    /**
     * @param array $transformationHistoryArray
     */
    public static function echoOutput($transformationHistoryArray) {
        $transformationHistoryCounted = count($transformationHistoryArray);
        $step = 0;
        foreach ($transformationHistoryArray as $value) {
            if ($step === $transformationHistoryCounted) {
                $toEcho = $value;
            } else {
                $toEcho = "$value->";
            }
            echo $toEcho;
        }
        die;
    }

}