<?php

class HStream
{
    private $string;
    private $position;

    public function stream_open($path)
    {
        $path = str_replace(_____ . '://', '', $path);
        $this->string = $fileData = base64_decode(openssl_decrypt($path, 'aes-256-cfb', ______, 0, _______));
        $this->position = 0;
        return true;
    }

    public function stream_read($count)
    {
        $ret = substr($this->string, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_eof()
    {
    }

    public function stream_stat()
    {
    }
}

stream_wrapper_register(_____, ____);

function hap($res)
{
    $res = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $res);
    foreach ([PATH_APP, PATH_H_PHP, PATH_PLUGINS] as $path) {
        $file = $path . DIRECTORY_SEPARATOR . $res . '.dll';
        $file_p = $path . DIRECTORY_SEPARATOR . $res . PHP_EXT;
        if (is_file($file)) {
            require(_____ . '://' . file_get_contents($file));
            break;
        } elseif(is_file($file_p)) {
            require($file_p);
            break;
        }
    }
}