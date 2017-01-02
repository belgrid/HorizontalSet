<?php

/**
 * Expand or read from a horizontal scaled set
 */
class HorizontalSet
{

    /**
     * @param array $toArray // reference
     * @param $key
     * @param mixed $value
     * @param string $token
     * @return mixed
     */
    public static function write(&$toArray, $key, $value, $token = ".")
    {

        // explode the dotted string to an array
        $path = explode($token, $key);

        // Build up an anonymous function for iterating
        $lambda = function (&$array, $counter = 0) use (&$lambda, $value, $path) {

            if (isset($array[$path[$counter]])) {

                if ($counter < count($path) - 1) {
                    $lambda($array[$path[$counter]], $counter + 1);
                } else {
                    $array[$path[$counter]] = $value;
                }

            } else {
                if ($counter < count($path) - 1) {
                    $array[$path[$counter]][$path[$counter + 1]] = array();
                    $lambda($array[$path[$counter]], $counter += 1);
                } else {
                    $array[$path[$counter]] = $value;
                }

            }
        };

        // Call the first time ...
        $lambda($toArray);

    }

    /**
     * @param array $fromArray
     * @param string $search
     * @param string $seperator
     * @return null|string
     * @internal param null $writeDefault
     */
    public static function read($fromArray, $search = "", $seperator = '.')
    {

        // Explode
        $keyarray = explode($seperator, $search);

        // Array depth
        $countLevels = count($keyarray);

        // Current value
        $value = '';

        // Iterate over the array limited by the count of values
        for ($i = 0; $i < $countLevels; $i++) {
            if (isset($fromArray[$keyarray[$i]])) {
                $value = $fromArray[$keyarray[$i]];
                if (!empty($fromArray[$keyarray[$i]]) && (array)$fromArray[$keyarray[$i]] === $fromArray[$keyarray[$i]]) {
                    $fromArray = $fromArray[$keyarray[$i]];
                }
            }
        }

        // Else return the readed value
        return $value;

    }

    /**
     * @param $fromArray
     * @param $search
     * @param string $seperator
     */
    public static function delete(&$fromArray, $search, $seperator = ".")
    {
        $keyarray = explode($seperator, $search);

        $f = function (&$array, $counting = 0) use (&$f, $keyarray) {

            if ($counting == (count($keyarray) - 1)) {
                unset($array[$keyarray[$counting]]);
                return;
            }

            if (isset($array[$keyarray[$counting]]) && is_array($array[$keyarray[$counting]])) {
                $f($array[$keyarray[$counting]], ++$counting);
            }
        };

        $f($fromArray);
    }

}
