<?php
/**
 * PHP RewriteEngine for Nginx
 * This is index.php searher. Use this with try_files.
 * @author Egor Vasyakin <egor@evas-php.com>
 * @license CC-BY-4.0
 * @copyright Egor Vasyakin
 */

// max nesting level
$maxLevel = 10;

// url path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// if index file
if ($path == '/') {
    unset($maxLevel, $path);
    return 'global';
}

// current nesting level
$i = 1;

while (true) {
    // if path be local directory
    if (is_dir($_SERVER['DOCUMENT_ROOT'] . $path)) {
        $index = $_SERVER['DOCUMENT_ROOT'] . $path . '/index.php';
        // if has local index file
        if (is_file($index)) {
            $_SERVER['SCRIPT_FILENAME'] = $index;
            unset($maxLevel, $path, $i, $index);
            include $_SERVER['SCRIPT_FILENAME'];
            // return 'local';
            exit();
        }
    }

    // cut path
    $path = substr($path, 0, strrpos($path, '/'));

    // break if path is empty
    if (empty($path)) break;

    // increment nesting level. break if level more that max
    if (++$i >= $maxLevel) break;
}

// if break (404)
unset($maxLevel, $path, $i, $index);
// return false;

// 404 response
header('HTTP/1.1 404 Not Found');
echo '404. Not Found';
exit();
