<?php

namespace FelipeAzambuja;

class JS {

    /**
     * Return instance of Vue
     *
     * @param string $instance
     *
     * @return Vue
     */
    function vue ( $instance = 'vue' ) {
        return new Vue ( $instance );
    }

    /**
     * Return instance of jquery
     *
     * @param string $element
     *
     * @return jQuery
     */
    public function jquery ( $element = null ) {
        return new jQuery ( $element );
    }

    public function alert ( $content ) {
        echo 'alert(' . $this->prepare_value ( $content ) . ');' . PHP_EOL;
    }

    public function confirm ( $content , $done = '' , $page = '') {
        echo 'libpkj.confirm = confirm(' . $this->prepare_value ( $content ) . ');' . PHP_EOL;
        if ( $page === '' ) {
            echo 'libpkj.call("' . $done . '",{response:libpkj.confirm});' . PHP_EOL;
        } else {
            echo 'libpkj.call("' . $done . '",{response:libpkj.confirm},"' . $page . '");' . PHP_EOL;
        }
    }

    public function console ( $content ) {
        echo 'console.log(' . $this->prepare_value ( $content ) . ');' . PHP_EOL;
    }

    public function setInterval ( $action , $time , $data = [] , $page = '' ) {
        if ( $page === '' ) {
            echo 'libpkj.intervals["' . $action . '"] = setInterval(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value ( $data ) . ');
            },' . $time . ');' . PHP_EOL;
        } else {
            echo 'libpkj.intervals["' . $action . '"] = setInterval(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value ( $data ) . ',"' . $page . '");
            },' . $time . ');' . PHP_EOL;
        }
    }

    public function setTimeout ( $action , $time , $data = [] , $page = '' ) {
        if ( $page === '' ) {
            echo 'libpkj.timers["' . $action . '"] = setTimeout(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value ( $data ) . ');
            },' . $time . ');' . PHP_EOL;
        } else {
            echo 'libpkj.timers["' . $action . '"] = setTimeout(function(){
                libpkj.call("' . $action . '",' . $this->prepare_value ( $data ) . ',"' . $page . '");
            },' . $time . ');' . PHP_EOL;
        }
    }

    public function redirect ( $to , $data = [] ) {
        if ( count ( $data ) ) {
            $to .= '?' . http_build_query ( $data );
        }
        echo 'location.href = ' . $to . ';' . PHP_EOL;
    }

    public function var ( $name , $data ) {
        echo 'libpkj.data["' . $name . '"] = ' . $this->prepare_value ( $data ) . ';' . PHP_EOL;
    }

    public function __call ( $name , $arguments ) {
        //melhorar arguments
        $arguments = array_map ( function ($value) {
            return $this->prepare_value ( $value );
        } , $arguments );
        echo $name . "(" . implode ( "," , $arguments ) . ");" . PHP_EOL;
    }

    public static function __callStatic ( $name , $arguments ) {
        //melhorar arguments
        $arguments = array_map ( function ($value) {
            return $this->prepare_value ( $value );
        } , $arguments );
        echo $name . "(" . implode ( "," , $arguments ) . ");" . PHP_EOL;
    }

    public function prepare_value ( $value ) {
        return json_encode ( $value , JSON_UNESCAPED_UNICODE );
    }

}

class Vue {

    var $instance;

    function __construct ( $instance = 'vue' ) {
        $this->instance = $instance;
    }

    public function data ( $name , $value ) {
        echo $this->instance . '.$data.' . $name . ' = ' . js ()->prepare_value ( $value ) . ';' . PHP_EOL;
    }

    public function call ( $function , $param ) {
        js ()->console ( 'Not implemented' );
    }

}

class jQuery {

    var $element;

    public function __construct ( $element = null ) {
        $this->element = $element;
    }

    public function __call ( $name , $arguments ) {
        $arguments = array_map ( function ($value) {
            return json_encode ( $value , JSON_UNESCAPED_UNICODE );
        } , $arguments );

        if ( $this->element ) {
            if ( count ( $arguments ) > 0 ) {
                //melhorar arguments
                echo '$("' . $this->element . '").' . $name . "(" . implode ( "," , $arguments ) . ");" . PHP_EOL;
            } else {
                echo '$("' . $this->element . '").' . $name . "();" . PHP_EOL;
            }
        } else {
            if ( count ( $arguments ) > 0 ) {
                echo '$' . $name . ".();" . PHP_EOL;
            } else {
                //melhorar arguments

                echo '$' . $name . ".(" . implode ( "," , $arguments ) . ");" . PHP_EOL;
            }
        }
    }

}

class UploadParser {

    private $raw = "";

    function __construct ( $name , $array = null ) {
        if ( $array === null ) {
            $array = $_FILES;
        }
        $this->raw = isset ( $array[$name] ) ? $array[$name] : null;
    }

    /**
     * Get a extension of file
     * @return string
     */
    function ext () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        $ext = explode ( "/" , $this->mime () );
        $ext = $ext[1];
        if ( $ext === "vnd.oasis.opendocument.spreadsheet" ) {
            $ext = "ods";
        } else if ( $ext === "vnd.oasis.opendocument.text" ) {
            $ext = "odt";
        } else if ( $ext === "plain" ) {
            $ext = "txt";
        } else if ( $ext === "x-7z-compressed" ) {
            $ext = "7z";
        } else if ( $ext === "x-rar" ) {
            $ext = "rar";
        }
        return $ext;
    }

    function mime () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        return $this->raw['type'];
    }

    /**
     * Return base64 format of file
     * @return type
     */
    function base64 () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        return base64_encode ( $this->data () );
    }

    /**
     * Get binary of file
     * @return type
     */
    function data () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        return file_get_contents ( $this->raw['tmp_name'] );
    }

    /**
     * Try a get name of file
     * @return type
     */
    function name () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        return $this->raw['name'];
    }

    /**
     * Get my raw format dont use please
     * @return type
     */
    function raw () {
        return $this->raw;
    }

    /**
     *
     * @return int size in bytes
     */
    function size () {
        if ( ! $this->is_ok () ) {
            return false;
        }
        return $this->raw['size'];
    }

    function error_code () {
        if ( $this->raw === null ) {
            return 99;
        }
        return $this->raw['error'];
    }

    function error () {
        return $this->codeToMessage ( $this->error_code () );
    }

    function is_ok () {
        return $this->error_code () === 0;
    }

    private function codeToMessage ( $code ) {
        switch ( $code ) {
            case UPLOAD_ERR_OK:
                $message = 'No error';
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    /**
     * Save data in a file
     * @param type $fileName
     * @return boolean
     */
    function save ( $fileName = '' ) {
        if ( $fileName === '' ) {
            $fileName = $this->name ();
        }
        if ( strpos ( $fileName , '.' ) === false ) {
            $fileName = $fileName . '.' . $this->ext ();
        }
        file_put_contents ( $fileName , $this->data () );
    }

    /**
     *
     * @return \Intervention\Image\Image
     */
    function image () {
        return Intervention\Image\ImageManagerStatic::make ( $this->data () );
    }

}
