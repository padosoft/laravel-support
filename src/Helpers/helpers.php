<?php

/**
 * Helpers to Validate some data with laravel validator.
 *
 * @param string|array $fields
 * @param string|array $rules
 *
 * @return bool
 */
function validate($fields, $rules): bool
{
    if (!is_array($fields)) {
        $fields = ['default' => $fields];
    }

    if (!is_array($rules)) {
        $rules = ['default' => $rules];
    }

    return Validator::make($fields, $rules)->passes();
}

/**
 * getLocale
 * @return string
 */
function locale(): string
{
    return app()->getLocale();
}

/**
 * Return true if current user is logged, otherwise return false.
 * @return bool
 */
function userIsLogged() : bool
{
    if (Auth::guest()) {
        return false;
    }

    return true;
}

if (!function_exists('current_user')) {
    /**
     * Fetch currently logged in useruser()
     * if User not logged in return false.
     * Otherwise return
     * a) User object of currently logged in user if $field is null or empty
     * b) return single field user()->{$field} if $field is not empty and not null
     *
     * Returns false if not logged in
     * @param string $field
     * @return mixed
     */
    function current_user(string $field = '')
    {
        if (!Auth::check()) {
            return false;
        }

        if ($field == 'id') {
            return Auth::id();
        }

        $user = Auth::user();

        if ($field === null || $field == '') {
            return $user;
        }

        return $user->{$field};
    }
}

/**
 * Fetch log of database queries
 *
 * @param bool $last [false] - if true, only return last query
 * @return array of queries
 */
function queries($last = false)
{
    $queries = \DB::getQueryLog();

    foreach ($queries as &$query) {
        $query['look'] = query_interpolate($query['query'], $query['bindings']);
    }

    if ($last) {
        return end($queries);
    }
    return $queries;
}

/**
 * Echo log of database queries
 *
 * @return string
 */
function query_table() : string
{
    $queries = queries();
    $html = '<table style="background-color: #FFFF00;border: 1px solid #000000;color: #000000;padding-left: 10px;padding-right: 10px;width: 100%;">';
    foreach ($queries as $query) {
        $html .= '<tr style="border-top: 1px dashed #000000;"><td style="padding:8px;">' . e($query['look']) . '</td><td style="padding:8px;">' . e($query['time']) . '</td></tr>';
    }

    return $html . '</table>';
}

/**
 * Replaces any parameter placeholders in a query with the value of that
 * parameter. Useful for debugging. Assumes anonymous parameters from
 * $params are are in the same order as specified in $query
 * @author glendemon
 *
 * @param string $query The sql query with parameter placeholders
 * @param array $params The array of substitution parameters
 * @return string The interpolated query
 */
function query_interpolate($query, $params)
{
    $keys = array();
    $values = $params;
    foreach ($params as $key => $value) {
        if (is_string($key)) {
            $keys[] = '/:' . $key . '/';
        } else {
            $keys[] = '/[?]/';
        }
        if (is_array($value)) {
            $values[$key] = implode(',', $value);
        }
        if (is_null($value)) {
            $values[$key] = 'NULL';
        }
    }
    // Walk the array to see if we can add single-quotes to strings
    array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));
    $query = preg_replace($keys, $values, $query, 1, $count);
    return $query;
}
