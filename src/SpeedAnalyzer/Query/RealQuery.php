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

        $parameters = $this->sortParameters($parameters);

        $keys = $this->getKeys($parameters);
        $values = $this->getValues($parameters);

        if (count($parameters)) {
            return preg_replace($keys, $values, $statement);
        }

        return preg_replace($keys, $values, $statement, 1, $count);
    }

    /*
     * Sort query parameters
     *
     * Get longest keys first, so the regex replacement doesn't
     * cut markers (ex : replace ":username" with "'joe'name"
     * if we have a param name :user)
     *
     * @param array $parameters
     *
     * @return array
     */
    private function sortParameters(array $parameters)
    {
        if (is_string(key($parameters))) {
            uksort($parameters, function ($k1, $k2) {
                return strlen($k2) - strlen($k1);
            });
        }

        return $parameters;
    }

    /**
     * Return a list of query placeholder keys
     *
     * @param array $parameters
     *
     * @return array
     */
    private function getKeys(array $parameters)
    {
        $keys = [];

        foreach ($parameters as $key => $value) {
            // Check if named parameters (':param') are used
            if (is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
                continue;
            }

            // Anonymous parameters ('?') are used
            $keys[] = '/[?]/';
        }

        return $keys;
    }

    /**
     * Get the actual query values
     *
     * @param array $parameters
     *
     * @return array
     */
    private function getValues(array $parameters)
    {
        $values = [];

        foreach ($parameters as $key => $value) {
            // Bring parameter into human-readable format
            if (is_string($value)) {
                $values[] = "'" . addslashes($value) . "'";
            } elseif (is_int($value)) {
                $values[] = strval($value);
            } elseif (is_float($value)) {
                $values[] = strval($value);
            } elseif (is_array($value)) {
                $values[] = implode(',', $value);
            } elseif (is_null($value)) {
                $values[] = 'NULL';
            }
        }

        return $values;
    }
}
