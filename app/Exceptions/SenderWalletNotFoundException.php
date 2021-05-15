<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SenderWalletNotFoundException extends Exception
{
    public function report()
    {
        Log::debug("Sender wallet not found");
    }
}
