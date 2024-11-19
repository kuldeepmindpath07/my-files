<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CustomFormatter extends LineFormatter
{
    public function __construct()
    {
        $format = "ts=%datetime% level=%level_name% msg=\"%message%\" ip=%context.ip% method=%context.method% path=%context.path% status=%context.status% endpoint=%context.endpoint% traceId=%context.traceId% spanId=%context.spanId%\n";
        parent::__construct($format, null, true, true);
    }
}
