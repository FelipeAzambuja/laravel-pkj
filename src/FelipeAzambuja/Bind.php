<?php

namespace FelipeAzambuja;

class JS
{

    public function jquery($element = null)
    {
        return new jQuery($element);
    }

    public function alert($content)
    {
        echo 'alert(' . $this->prepare_value($content) . ');' . PHP_EOL;
    }

    public function confirm($content, $done = '', $page = '')
    {
        echo 'libpkj.confirm = confirm(' . $this->prepare_value($content) . ');' . PHP_EOL;
        if ($page === '') {
            echo 'libpkj.call("' . $done . '",{response:libpkj.confirm});' . PHP_EOL;
        } else {
            echo 'libpkj.call("' . $done . '",{response:libpkj.confirm},"' . $page . '");' . PHP_EOL;
        }
    }

    public function console($content)
    {
        echo 'console.log(' . $this->prepare_value($content) . ');' . PHP_EOL;
    }

    public function setInterval($action, $time, $data = [], $page = '')
    {
        if ($page === '') {
            echo 'libpkj.intervals["' . $action . '"] = setInterval(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value($data) . ');
            },' . $time . ');' . PHP_EOL;
        } else {
            echo 'libpkj.intervals["' . $action . '"] = setInterval(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value($data) . ',"' . $page . '");
            },' . $time . ');' . PHP_EOL;
        }
    }

    public function setTimeout($action, $time, $data = [], $page = '')
    {
        if ($page === '') {
            echo 'libpkj.timers["' . $action . '"] = setTimeout(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value($data) . ');
            },' . $time . ');' . PHP_EOL;
        } else {
            echo 'libpkj.timers["' . $action . '"] = setTimeout(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value($data) . ',"' . $page . '");
            },' . $time . ');' . PHP_EOL;
        }
    }
    public function var($name, $data)
    {
        echo 'libpkj.data["'.$name.'"] = '.$this->prepare_value($data).';'.PHP_EOL;
    }
    public function __call($name, $arguments)
    {
        //melhorar arguments
        echo $name . "('" . implode("','", $arguments) . "');" . PHP_EOL;
    }
    public static function __callStatic($name, $arguments)
    {
        //melhorar arguments
        echo $name . "('" . implode("','", $arguments) . "');" . PHP_EOL;
    }

    public function prepare_value($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
class jQuery
{

    var $element;

    public function __construct($element = null)
    {
        $this->element = $element;
    }

    public function __call($name, $arguments)
    {
        if ($this->element) {
            if (count($arguments) > 0) {
                //melhorar arguments
                echo '$("' . $this->element . '").' . $name . "('" . implode("','", $arguments) . "');" . PHP_EOL;
            } else {
                echo '$("' . $this->element . '").' . $name . "();" . PHP_EOL;
            }
        } else {
            if (count($arguments) > 0) {
                echo '$' . $name . ".();" . PHP_EOL;
            } else {
                //melhorar arguments
                echo '$' . $name . ".('" . implode("','", $arguments) . "');" . PHP_EOL;
            }
        }
    }
}
