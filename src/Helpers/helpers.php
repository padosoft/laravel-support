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
    $locale = app()->getLocale();
    if(!$locale){
        return config('app.fallback_locale');
    }
    return $locale;
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
 * Fetch log of database queries and return executed queries
 *
 * Usage:
 *
 * You need enable query log by calling:
 * \DB::enableQueryLog();
 *
 * If you have more than one DB connection you must specify it and Enables query log for my_connection
 * \DB::connection('my_connection')->enableQueryLog();
 *
 * query the db
 * \App\Articoli::query()->where('id','=',343242342)->get();
 *
 * then you can call queries() or in case of more than oine db call queries($last, 'my_connection')
 * dd(queries($last));
 *
 * the output is an array:
 * [
 *   "query" => "select * from `negozi` where `id` = ?",
 *   "bindings" => [343242342,],
 *   "time" => 1.77,
 *   "look" => "select * from `negozi` where `id` = 343242342",
 * ]
 *
 * If you want to print only interpolated query
 * echo queries(true)['look'] //output: "select * from `negozi` where `id` = 343242342"
 *
 * For performance and memory reasons, after get queries info, you can disable query log by excecute
 * \DB::disableQueryLog();
 * or in case of more db connections:
 * \DB::connection('my_connection')->disableQueryLog();
 *
 * @param bool $last [false] - if true, only return last query
 * @param string $dbConnectionName if empty use default connection, otherwise if you are multiple DB connections you may specify it.
 * @return array of queries
 */
function queries($last = false, $dbConnectionName = '')
{
    if($dbConnectionName!=''){
        $queries = \DB::connection($dbConnectionName)->getQueryLog();
    }else{
        $queries = \DB::getQueryLog();
    }

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
    array_walk($values, function(&$v) { if (!is_numeric($v) && $v!="NULL") $v = "'".$v."'";});
    $query = preg_replace($keys, $values, $query, 1, $count);
    return $query;
}
