<?php

use FelipeAzambuja\jQuery;

$controllers = [];
$arquivos = glob('../app/Http/Controllers/*.php');
$arquivos = array_merge($arquivos, glob('../app/Modules/*/Http/Controllers/*.php'));
foreach ($arquivos as $arquivo) {
    $namespace = '';
    $class = '';
    $functions = [];
    $path = str_replace(['../app/Http/Controllers/', '../app/Modules/', 'Http/Controllers/', '.php'], '', $arquivo);
    $path = '/' . strtolower($path);
    foreach (explode("\n", file_get_contents($arquivo)) as $value) {
        $value = explode(' ', trim($value));
        if ($namespace === '' && $value[0] === 'namespace') {
            $namespace = str_replace(';', '', $value[1]);
        }
        if ($class === '' && $value[0] === 'class') {
            $class = $value[1];
        }
        if ($namespace !== '' && $class !== '') {
            if ($value[0] === 'function') {
                $functions[] = trim(explode('(', $value[1])[0]);
            }
        }
    }
    $controllers[] = [
        'file' => $arquivo,
        'class' => $class,
        'namespace' => '\\' . $namespace,
        'functions' => $functions,
        'path' => $path
    ];
}
foreach ($controllers as $c) {
    foreach ($c['functions'] as $f) {
        if ($f === 'index') {
            Route::any($c['path'], $c['namespace'] . '\\' . $c['class'] . '@index');
        } else {
            Route::any($c['path'] . '/' . $f, $c['namespace'] . '\\' . $c['class'] . '@' . $f);
        }
    }
}
function pkj(){
    echo '<meta name="csrf_token" content="'.csrf_token().'" >';
    echo '<script src="'.route('libpkj').'" ></script>';
}
Route::get('/laravel-pkj/lib', function () {
    ob_start();
    echo 'var libpkj = {};' . PHP_EOL;
    echo 'libpkj.base = "' . url('') . '";' . PHP_EOL;
    //echo 'libpkj.csrf_token = "'.csrf_token().'";'.PHP_EOL;
    echo file_get_contents(__DIR__.'/libpkj.js');
    return response(ob_get_clean())->header('Content-type', 'application/javascript');
})->name('libpkj');



/**
 * Return instance of js
 *
 * @return JS
 */
function js()
{
    return new \FelipeAzambuja\JS();
}


/**
 * Return instance of jquery
 *
 * @param string $element
 *
 * @return jQuery
 */
function jquery($element = null){
    return new jQuery($element);
}
