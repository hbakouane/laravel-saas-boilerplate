<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $customer = auth()->user();
        return view('settings.index', [
            'intent' => $customer->createSetupIntent(),
            'customer' => $customer
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $paymentMethod = $request->paymentMethod['id'];
        if ($user->hasPaymentMethod()) {
            // Add a payment method
            $user->addPaymentMethod($paymentMethod);
        }
        // Make the payment method default
        $user->updateDefaultPaymentMethod($paymentMethod);
        $user->update(['payment_method' => $paymentMethod]);
        return 'success';
    }

    public function deletePaymentMethod(Request $request)
    {
        $user = User::find($request->customer_id);
        $user->update(['payment_method' => null]);
        $user->deletePaymentMethods();
        return back()->with('response', 'Payment method deleted successfully!');
    }
}
