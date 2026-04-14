<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function __construct(private RazorpayService $razorpay) {}

    public function createOrder(Request $request)
    {
        $request->validate(['plan' => 'required|in:starter,growth,agency']);

        $user = Auth::user();

        try {
            $order = $this->razorpay->createOrder(
                $request->plan,
                $user->id,
                $user->email
            );

            return response()->json($order, 201);

        } catch (\Exception $e) {
            Log::error('Razorpay order failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create payment order.'], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'plan'                => 'required|in:starter,growth,agency',
        ]);

        $user = Auth::user();

        $valid = $this->razorpay->verifySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (!$valid) {
            return response()->json([
                'success' => false,
                'error'   => 'Payment verification failed.',
            ], 400);
        }

        $planConfig = config("postpilot.plans.{$request->plan}");

        // Save payment record
        $payment = Payment::create([
            'user_id'    => $user->id,
            'payment_id' => $request->razorpay_payment_id,
            'order_id'   => $request->razorpay_order_id,
            'plan'       => $request->plan,
            'amount'     => $planConfig['amount_paise'],
            'currency'   => 'INR',
            'status'     => 'success',
            'signature'  => $request->razorpay_signature,
        ]);

        // Upgrade user plan
        $user->upgradePlan($request->plan);

        // Send receipt email (queued)
        try {
            Mail::to($user->email)->queue(new PaymentReceiptMail($user, $payment));
        } catch (\Exception $e) {
            Log::warning('Receipt email failed', ['error' => $e->getMessage()]);
        }

        // Admin alert (fire and forget)
        try {
            Mail::to(config('mail.from.address'))->queue(
                new \App\Mail\AdminAlertMail(
                    'New Payment Received',
                    [
                        'User'       => $user->email,
                        'Plan'       => $planConfig['label'],
                        'Amount'     => $payment->amountInr(),
                        'Payment ID' => $payment->payment_id,
                        'Time'       => now()->setTimezone('Asia/Kolkata')->format('d M Y, h:i A'),
                    ]
                )
            );
        } catch (\Exception $e) {}

        Log::info('Payment verified', [
            'user_id'    => $user->id,
            'plan'       => $request->plan,
            'payment_id' => $request->razorpay_payment_id,
        ]);

        return response()->json([
            'success'    => true,
            'plan'       => $request->plan,
            'post_quota' => $planConfig['post_quota'],
            'payment_id' => $payment->payment_id,
        ]);
    }
}
