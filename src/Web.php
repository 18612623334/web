<?php

namespace wangliang\Web;

use Illuminate\Session\SessionManager;
use Illuminate\Config\Repository;

class Web
{
    /**
     * @param string $msg
     * @return string
     */
    public function test_rtn($msg = ''){
        echo 'running' . "\n";
    }
}