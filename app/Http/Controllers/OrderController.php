<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function payment(Order $order)
    {
        // Ensure user owns this order, if it's tied to a user
        if ($order->user_id !== null && $order->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow payment if pending
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order) // Assuming 'orders.show' is mapped to AccountController@show
                ->with('info', 'Payment info already submitted or order paid.');
        }

        return view('orders.payment', [
            'order' => $order,
            'bkash_number' => Setting::get('bkash_number'),
            'nagad_number' => Setting::get('nagad_number'),
        ]);
    }

    public function submitPayment(Request $request, Order $order)
    {
        if ($order->user_id !== null && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_sender_number' => 'required|string|max:20',
            'payment_amount' => 'required|numeric|min:0',
            'payment_transaction_id' => 'nullable|string|max:50',
        ]);

        $order->update([
            'payment_sender_number' => $request->payment_sender_number,
            'payment_amount' => $request->payment_amount,
            'payment_transaction_id' => $request->payment_transaction_id,
            'payment_status' => 'pending_verification', // New status from migration
            'delivery_charge_prepaid' => true,
        ]);

        return redirect()->route('order.success', $order) // Assuming order.success displays confirmation
            ->with('success', 'Payment info submitted. Awaiting verification.');
    }
}
