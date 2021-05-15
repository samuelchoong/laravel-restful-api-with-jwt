<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class WalletDoesNotHaveEnoughBalanceException extends Exception
{
    public function report()
    {
        Log::debug("Insufficient Wallet Balance");
    }
}
