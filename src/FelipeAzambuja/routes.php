<?php
ignore_user_abort(true);
use Illuminate\Http\Request;
use FelipeAzambuja\jQuery;
use Illuminate\Foundation\Console\Presets\Vue;

$controllers = [];
$arquivos = glob('../app/Http/Controllers/*.php');
$arquivos = array_merge($arquivos, glob('../app/Modules/*/Http/Controllers/*.php'));
foreach ($arquivos as $arquivo) {
    $namespace = '';
    $class = '';
    $functions = [];
    $path = str_replace(['../app/Http/Controllers/', '../app/Modules/', 'Http/Controllers/', 'Controller','.php',], '', $arquivo);
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
            Route::any($c['path'], $c['namespace'] . '\\' . $c['class'] . '@index')->middleware('web');
        } else {
            Route::any($c['path'] . '/' . $f, $c['namespace'] . '\\' . $c['class'] . '@' . $f)->middleware('web');
        }
    }
}
function pkj()
{
    echo '<meta name="csrf_token" content="' . csrf_token() . '" >';
    echo '<script src="' . route('libpkj') . '" ></script>';
}
Route::get('/laravel-pkj/lib', function () {
    ob_start();
    echo 'var libpkj = {};' . PHP_EOL;
    echo 'libpkj.base = "' . url('') . '";' . PHP_EOL;
    //echo 'libpkj.csrf_token = "'.csrf_token().'";'.PHP_EOL;
    echo file_get_contents(__DIR__ . '/libpkj.js');
    return response(ob_get_clean())->header('Content-type', 'application/javascript');
})->name('libpkj');



/**
 * Return instance of js
 *
 * @return \FelipeAzambuja\JS
 */
function js()
{
    return new \FelipeAzambuja\JS();
}
/**
 * Return instance of  Vue
 *
 * @param string $instance
 *
 * @return \FelipeAzambuja\Vue
 */
function vue($instance = 'vue', $name = null, $value = '')
{
    if ($name !== null) {
        (new \FelipeAzambuja\Vue($instance))->data($name, $value);
    } else {
        return new \FelipeAzambuja\Vue($instance);
    }
}
/**
 * Return instance of jquery
 *
 * @param string $element
 *
 * @return \FelipeAzambuja\jQuery
 */
function jquery($element = null)
{
    return new \FelipeAzambuja\jQuery($element);
}

/**
 * Return a instance of upload parser
 * @param type $value
 * @return \FelipeAzambuja\UploadParser
 */
function upload_parser($value)
{
    return new UploadParser($value);
}
/**
 * Execute Async functions on Windows
 *
 * @param string|self|this $class class name
 * @param string $function function_name
 * @param array $data
 *
 * @return void
 */
function async($class, $function, $data = [])
{
    $class = new ReflectionClass($class);
    $data = [
        'data' => $data,
        'class' => $class->name,
        'function' => $function,
        '_GET' => $_GET,
        '_POST' => $_POST
    ];

    $curl = curl_init(url('/async'));
    curl_setopt_array($curl, [
        CURLOPT_TIMEOUT_MS => 5,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true
    ]);
    echo curl_exec($curl);
    curl_close($curl);
}
Route::post('/async', function (Request $form) {
    ignore_user_abort(true);
    $f = new ReflectionMethod($form->class, $form->function);
    foreach ($form->all() as $k => $v) {
        $form->{$k} = $v;
    }
    $form->async = true;
    $c = (new ReflectionClass($form->class))->newInstance();
    $f->invokeArgs($c, [$form]);
    exit();
});
