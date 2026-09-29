<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\WalletTransactionResource;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends BaseApiController
{
    /**
     * Get Wallet Balance & Transaction History
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return $this->success([
            'wallet_balance' => (float) $user->wallet_balance,
            'currency' => 'SAR',
            'transactions' => WalletTransactionResource::collection($transactions),
        ], 'Success', 200, [
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'total' => $transactions->total(),
        ]);
    }
}
