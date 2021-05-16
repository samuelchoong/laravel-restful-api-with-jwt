<?php

namespace App\Services;

use App\Models\Wallet;
use App\Exceptions\SenderWalletNotFoundException;
use App\Exceptions\ReceiverWalletNotFoundException;
use App\Exceptions\WalletDoesNotHaveEnoughBalanceException;
use App\Exceptions\CantTransferToSameWalletException;
use Illuminate\Support\Facades\DB;
class WalletService{
    public function transfer(int $senderWalletId, int $receiverWalletId, float $transferAmount) : array
    {
        try{
            DB::beginTransaction();

            //check if user try send to own wallet
            if($senderWalletId == $receiverWalletId){
                throw new CantTransferToSameWalletException('Can not transfer between same wallet');
            }

            //check if sender wallet exist
            $senderWallet = Wallet::find($senderWalletId);
            if(!$senderWallet){
                throw new SenderWalletNotFoundException('Sender Wallet not found!');
            }

            //check if receiver wallet exist
            $receiverWallet = Wallet::find($receiverWalletId);
            if(!$receiverWallet){
                throw new ReceiverWalletNotFoundException('Receiver Wallet not found!');
            }

            //check if sender has enough balance
            $senderHasEnoughBalance = $this->hasEnoughBalanceToTransfer($senderWalletId,$transferAmount);
            if(!$senderHasEnoughBalance){
                throw new WalletDoesNotHaveEnoughBalanceException('Sender does not have enough balance!');
            }

            //decrease sender balance
            $senderWallet->decrement('balance',$transferAmount);

            //increase receiver balance
            $receiverWallet->increment('balance',$transferAmount);

            //commit
            DB::commit();

            return ['success'=>true,'message'=>'Balance has been transferred!'];

        }catch(\Exception $e){
            DB::rollback();
            return ['success'=>false,'message'=>$e->getMessage()];
        }
    }

    public function hasEnoughBalanceToTransfer(int $senderWalletId, float $transferAmount) : bool
    {
        $senderWallet = Wallet::find($senderWalletId);
        return ($senderWallet->balance >= $transferAmount) ? true : false;
    }
}
