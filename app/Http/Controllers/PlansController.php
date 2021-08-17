<?php

namespace App\Http\Controllers;

use App\Http\Middleware\redirectIfHasntFilledCreditCard;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $plans = Plan::orderBy('id', 'DESC')->get();
        $subscribed = false;
        $subscribed_plan = null;
        foreach($plans as $plan) {
            if ($user->subscribed($plan->title)) {
                $subscribed = true;
                $subscribed_plan = $plan->title;
            }
        }
        $hasPaymentMethod = ! empty($user->payment_method) ? true : false;
        return view('plans.index', [
            'plans' => $plans,
            'subscribed' => $subscribed,
            'subscribed_plan' => $subscribed_plan,
            'hasPaymentMethod' => $hasPaymentMethod
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $stripe_product = $stripe->products->create([
            'name' => $data['title'],
        ]);
        $stripe_plan = $stripe->plans->create([
            'amount' => $data['price'],
            'currency' => 'usd',
            'interval' => $data['interval'],
            'product' => $stripe_product->id
        ]);
        $data['stripe_plan_id'] = $stripe_plan->id;
        $data['stripe_product_id'] = $stripe_product->id;
        $plan = (new Plan())->create($data);
        return back();
    }

    public function destroy(Request $request)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $stripe->plans->delete($request->stripe_plan_id);
        Plan::where('stripe_plan_id', $request->stripe_plan_id)->delete();
        return back();
    }

    public function subscribe(Request $request)
    {
        // Get the plan
        $plan = Plan::find($request->plan_id);
        // Get the user's default payment method
        $paymentMethod = auth()->user()->payment_method;
        // Try to subscribe the user
        try {
            $request->user()->newSubscription(
                $plan->title, $plan->stripe_plan_id
            )->create($paymentMethod);
        } catch (\Exception $e) {
            throw $e;
        }
        return back();
    }

    public function cancelSubscription(Request $request)
    {
        auth()->user()->subscription($request->planToCancel)->cancelNow();
        if (auth()->user()->subscription($request->planToCancel)->cancelled()) {
            return back();
        }
        return false;
    }
}
