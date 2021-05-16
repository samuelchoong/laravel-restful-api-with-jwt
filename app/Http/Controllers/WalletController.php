<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Jobs\BalanceTransferJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    private $walletService;

    public function __construct()
    {
        $this->walletService = new WalletService();
    }

    public function transferBalance(Request $request)
    {
        // try{
        //     $transferResponse = $this->walletService->transfer(
        //         $request->fromWalletId,
        //         $request->toWalletId,
        //         $request->transferAmount,
        //     );
        //     return response()->json($transferResponse);
        // }catch(\Exception $e){
        //     return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        // }
        try{
            dispatch(new BalanceTransferJob($request->fromWalletId,$request->toWalletId,$request->transferAmount))
            ->onQueue('transfer-balance')
            ->delay(Carbon::now()->addSeconds(3));
            return response()->json([
                'success' => true,
                'message' => 'Balance transfer request has been submitted. Please wait for your transaction'
            ]);

        }catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }
}
