<?php

namespace Rgasch\FlexInput;


class Input
{
    static $defaultFilter = FILTER_SANITIZE_STRING;
    static $defaultFlags  = FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;


    /**
     * Wrapper for COOKIE input 
     */
    public static function cookie ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            return self::filterArray ($_COOKIE, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'C', $filter, $args);
    }


    /**
     * Wrapper for DELETE input 
     */
    public static function delete ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            $values = array();
            parse_str (file_get_contents("php://input"), $values);
            return self::filterArray ($values, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'D', $filter, $args);
    }


    /**
     * Wrapper for FILES input 
     */
    public static function files ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            return self::filterArray ($_FILES, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'F', $filter, $args);
    }


    /**
     * Wrapper for GET input 
     */
    public static function get ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            return self::filterArray ($_GET, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'G', $filter, $args);
    }


    /**
     * Wrapper for POST input 
     */
    public static function post ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            return self::filterArray ($_POST, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'P', $filter, $args);
    }


    /**
     * Wrapper for PUT input 
     */
    public static function put ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            $values = array();
            parse_str (file_get_contents("php://input"), $values);
            return self::filterArray ($values, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'U', $filter, $args);
    }


    /**
     * Wrapper for REQUEST input 
     */
    public static function request ($key, $default=null, int $filter=null, $args=array())
    {
        if (!$key) {
            return self::filterArray ($_REQUEST, $filter, $args);
        }

        return self::getPassedValue ($key, $default, 'R', $filter, $args);
    }


    /**
     * Return the requested key using filter_var() from input in a safe way.
     *
     * This function is safe to use for recursive arrays and either returns a non-empty string (or array) or the (optional) default.
     *
     * @param string $key        The input field to return.
     * @param mixed  $default    The value to return if the requested field is not found (optional) (default=false).
     * @param string $source     The source field to get a parameter from (optional) (default=null)
     * @param string $filter     The filter directive to apply to the retrieved input (optional) (default=null)
     * @param array  $args       The filter processing args to apply (optional) (default=array())
     *
     * @return mixed The requested input key or the specified default.
     */
    public static function getPassedValue ($key, $default=null, String $source=null, int $filter=null, $args=array())
    {
        if (!$key) {
            throw new \Exception ('Empty key passed to Input::getPassedValue()');
        }

        if (!$source) {
            $source = 'REQUEST';
        } else {
            $source = strtoupper ($source);
        }

        if (!$filter) {
            $filter = self::$defaultFilter;
        }

        if (!$args) {
            $args = array ('flags' => self::$defaultFlags);
        }
        $_args = $args;

        switch (true) {
            case (isset($_REQUEST[$key]) && !isset($_FILES[$key]) && ($source == 'R' || $source == 'REQUEST')):
                if (is_array($_REQUEST[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = filter_var ($_REQUEST[$key], $filter, $args);
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            case isset($_GET[$key]) && ($source == 'G' || $source == 'GET'):
                if (is_array($_GET[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = filter_var ($_GET[$key], $filter, $args);
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            case isset($_POST[$key]) && ($source == 'P' || $source == 'POST'):
                if (is_array($_POST[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = filter_var ($_POST[$key], $filter, $args);
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            case isset($_COOKIE[$key]) && ($source == 'C' || $source == 'COOKIE'):
                if (is_array($_COOKIE[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = filter_var ($_COOKIE[$key], $filter, $args);
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            case isset($_FILES[$key]) && ($source == 'F' || $source == 'FILES'):
                if (is_array($_FILES[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = $_FILES[$key];
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            case (isset($_GET[$key]) || isset($_POST[$key])) && ($source == 'GP' || $source == 'GETPOST'):
                if (isset($_GET[$key])) {
                    if (is_array($_GET[$key])) {
                        $args['flags'] = FILTER_REQUIRE_ARRAY;
                    }
                    $value = filter_var ($_GET[$key], $filter, $args);
                    if (is_array($value)) {
                        $value = self::filterArray ($value, $filter, $_args);
                    }
                }
                if (isset($_POST[$key])) {
                    if (is_array($_POST[$key])) {
                        $args['flags'] = FILTER_REQUIRE_ARRAY;
                    }
                    $value = filter_var ($_POST[$key], $filter, $args);
                    if (is_array($value)) {
                        $value = self::filterArray ($value, $filter, $_args);
                    }
                }
                break;

            case ($source == 'D' || $source == 'DELETE'):
            case ($source == 'U' || $source == 'PUT'):    // P is already taken by POST, so we use U for pUt.
                $values = array();
                parse_str (file_get_contents("php://input"), $values);
                if (is_array($values[$key])) {
                    $args['flags'] = FILTER_REQUIRE_ARRAY;
                }
                $value = filter_var ($values[$key], $filter, $args);
                if (is_array($value)) {
                    $value = self::filterArray ($value, $filter, $_args);
                }
                break;

            default:
                if ($source) {
                    static $valid = array('R', 'REQUEST', 'G', 'GET', 'P', 'POST', 'C', 'U', 'PUT', 'D', 'DELETE', 'COOKIE', 'F', 'FILES', 'GP', 'GETPOST');
                    if (!in_array($source, $valid)) {
                        throw new \Exception ("Invalid input source [$source] received");
                    }
                }
                $value = $default;
        }


        return $value;
    }


    protected static function filterArray (array $values, int $filter=null, $args=array())
    {
        if (!$values) {
            return $values;
        }

        if (!$filter) {
            $filter = $defaultFilter; 
        }

        if (!$args) {
            $args = array ('flags' => self::$defaultFlags);
        }

        foreach ($values as $k=>$v) {
            $values[$k] = filter_var ($v, $filter, $args);
        }

        return $values;
    }
}

