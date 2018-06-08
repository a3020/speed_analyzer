<?php

namespace A3020\SpeedAnalyzer\Query;

class RealQuery
{
    /**
     * Returns the emulated SQL string
     *
     * @param string $statement
     * @param array $parameters
     *
     * @return string
     */
    public function getSqlQuery($statement, $parameters = [])
    {
        if (!is_array($parameters) || count($parameters) === 0) {
            return $statement;
        }

        $keys = array();
        $values = array();
        /*
         * Get longest keys first, sot the regex replacement doesn't
         * cut markers (ex : replace ":username" with "'joe'name"
         * if we have a param name :user )
         */
        $isNamedMarkers = false;
        if (count($parameters) && is_string(key($parameters))) {
            uksort($parameters, function($k1, $k2) {
                return strlen($k2) - strlen($k1);
            });
            $isNamedMarkers = true;
        }
        foreach ($parameters as $key => $value) {
            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
            } else {
                $keys[] = '/[?]/';
            }
            // bring parameter into human-readable format
            if (is_string($value)) {
                $values[] = "'" . addslashes($value) . "'";
            } elseif(is_int($value)) {
                $values[] = strval($value);
            } elseif (is_float($value)) {
                $values[] = strval($value);
            } elseif (is_array($value)) {
                $values[] = implode(',', $value);
            } elseif (is_null($value)) {
                $values[] = 'NULL';
            }
        }
        if ($isNamedMarkers) {
            return preg_replace($keys, $values, $statement);
        } else {
            return preg_replace($keys, $values, $statement, 1, $count);
        }
    }
}
