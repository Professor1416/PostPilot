<?php

namespace App\Http\Controllers;

use App\Models\ConnectedAccount;
use App\Services\MetaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function __construct(private MetaService $meta) {}

    public function redirectToInstagram()
    {
        return redirect($this->meta->getOAuthUrl());
    }

    public function handleInstagramCallback(Request $request)
    {
        if ($request->error) {
            return redirect()->route('dashboard.accounts')
                ->with('error', 'Instagram connection was cancelled.');
        }

        $request->validate(['code' => 'required|string', 'state' => 'required|string']);

        // CSRF state check
        if ($request->state !== session('_token')) {
            return redirect()->route('dashboard.accounts')
                ->with('error', 'Invalid state. Please try again.');
        }

        $user = Auth::user();

        // Check account limit
        $currentCount = $user->connectedAccounts()->count();
        if ($currentCount >= $user->account_limit) {
            return redirect()->route('dashboard.accounts')
                ->with('error', "Account limit reached for your {$user->plan} plan. Upgrade to connect more.");
        }

        try {
            // Exchange code for short token
            $shortToken = $this->meta->exchangeCodeForToken(
                $request->code,
                config('postpilot.meta.redirect_uri')
            );

            // Get long-lived token
            ['token' => $longToken, 'expires_at' => $expiresAt] =
                $this->meta->getLongLivedToken($shortToken);

            // Get Instagram account info
            $accountInfo = $this->meta->getInstagramAccountInfo($longToken);

            // Upsert account (refresh token if already exists)
            ConnectedAccount::updateOrCreate(
                [
                    'user_id'          => $user->id,
                    'platform_user_id' => $accountInfo['platform_user_id'],
                    'platform'         => 'instagram',
                ],
                [
                    'account_name'        => $accountInfo['account_name'],
                    'profile_picture_url' => $accountInfo['profile_picture_url'],
                    'access_token'        => $longToken,
                    'token_expires_at'    => $expiresAt,
                    'is_active'           => true,
                ]
            );

            Log::info('Instagram account connected', ['user_id' => $user->id]);

            return redirect()->route('dashboard.accounts')
                ->with('success', "Instagram @{$accountInfo['account_name']} connected successfully!");

        } catch (\Exception $e) {
            Log::error('Instagram connect failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard.accounts')
                ->with('error', $e->getMessage());
        }
    }

    public function disconnect(ConnectedAccount $account)
    {
        $user = Auth::user();

        if ($account->user_id !== $user->id) {
            abort(403);
        }

        $account->update(['is_active' => false]);

        // Move scheduled posts for this account to draft
        $affected = \App\Models\Post::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->whereJsonContains('connected_account_ids', $account->id)
            ->update(['status' => 'draft']);

        Log::info('Account disconnected', ['account_id' => $account->id, 'posts_affected' => $affected]);

        return redirect()->route('dashboard.accounts')
            ->with('success', "Account disconnected. {$affected} scheduled post(s) moved to draft.");
    }
}
