<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WalletService;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class BalanceTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $senderWalletId,$receiverWalletId,$transferAmount,$walletService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($senderWalletId,$receiverWalletId,$transferAmount)
    {
        $this->walletService = new WalletService();
        $this->senderWalletId = $senderWalletId;
        $this->receiverWalletId = $receiverWalletId;
        $this->transferAmount = $transferAmount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            DB::beginTransaction();
            $transferResponse = $this->walletService->transfer($this->senderWalletId,$this->receiverWalletId,$this->transferAmount);
            if($transferResponse['success']==true){
                DB::commit();
            }else{
                DB::rollback();
            }
        }catch(\Exception $e){
            DB::rollback();
        }
    }
}
