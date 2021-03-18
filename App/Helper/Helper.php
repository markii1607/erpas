<?php 

    /**
     * `snakeCaseToPascalCase` Convert `snake_case` to `PascalCase`.
     * @param  string $val
     * @return string
     */
    function snakeCaseToPascalCase($val = '')
    {
        return str_replace('_', '', ucwords($val, '_'));
    }

    /**
     * `snakeCaseToCamelCase` Convert `snake_case` to `camelCase`.
     * @param  string $val
     * @return string
     */
    function snakeCaseToCamelCase($val = '')
    {
        return str_replace('_', '', lcfirst(ucwords($val, '_')));
    }

    /**
     * `pascalCaseToSnakeCase` Convert `PascalCase` to `snake_case`.
     * @param  string $val
     * @return string
     */
    function pascalCaseToSnakeCase($val = '')
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $val));
    }

    /**
     * `pascalCaseToCamelCase` Convert `PascalCase` to `camelCase`.
     * @param  string $val
     * @return string
     */
    function pascalCaseToCamelCase($val = '')
    {
        return lcfirst($val);
    }

    /**
     * `camelCaseToSnakeCase` Convert `camelCase` to `snake_case`.
     * @param  string $val
     * @return string
     */
    function camelCaseToSnakeCase($val = '')
    {
        return lcfirst(strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $val)));
    }

    /**
     * `camelCaseToPascalCase` Convert `camelCase` to `PascalCase`.
     * @param  string $val
     * @return string
     */
    function camelCaseToPascalCase($val = '')
    {
        return ucfirst($val);
    }

        /**
     * function for beautifier of arrays
     * @fn_print_r(var);
     */
    function fn_print_r()
    {
        static $count = 0;
        $args = func_get_args();

        if (!empty($args)) {
            echo '<ol id="fn_print_r" style="font-family: Courier; font-size: 12px; border: 1px solid #dedede; background-color: #efefef; float: left; padding-right: 20px;">';
            foreach ($args as $k => $v) {
                $v = htmlspecialchars(print_r($v, true));
                if ($v == '') {
                    $v = '    ';
                }

                echo '<li><pre>' . $v . "\n" . '</pre></li>';
            }
            echo '</ol><div style="clear:left;"></div>';
        }
        $count++;
    }

    function fn_print_die()
    {
        $args = func_get_args();
        call_user_func_array('fn_print_r', $args);
        die();
    }

    function dd() {
        $args = func_get_args();
        call_user_func_array('fn_print_die', $args);
    }
?>
