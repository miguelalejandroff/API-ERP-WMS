<?php

namespace App\Logging;

use App\Mail\OrderShipped;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Logger;

class CustomMailerHandler extends NativeMailerHandler
{

    protected function send(string $content, array $records): void
    {
        Mail::to($this->to)->send(new OrderShipped);
    }
    protected function getDefaultFormatter(): HtmlFormatter
    {
        return new HtmlFormatter();
    }
}
