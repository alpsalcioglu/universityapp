<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */

function basePath($path = '')
{
    return __DIR__ . '/' . $path;
}

/**
 * Load a partial 
 * @param string $name
 * @return void
 * 
 */

function loadPartial($name, $data = [])
{
    $partialPath =  basePath("/templates/partials/{$name}.php");
    if (file_exists($partialPath)) {
        extract($data);
        require $partialPath;
    } else {
        echo "Partial '{$name} not found!'";
    }
}

/**
 * Inspect a value(s)
 *@param mixed $value
 *@return void
 */

function inspect($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

/**
 * Inspect a value(s) and die
 *@param mixed $value
 *@return void
 */

function inspectAndDie($value)
{
    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
}
