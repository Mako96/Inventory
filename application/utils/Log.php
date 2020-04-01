<?php

class Log
{
    public function __construct($data)
    {
        file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode($data), FILE_APPEND);
    }
}
