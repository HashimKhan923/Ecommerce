<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use App\Models\OrderStatus;
use Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class OrderController extends Controller
{


    public function index($shop_id = null, $start = 0, $length = 10, $status = null, $searchValue = null)
    {
        $order = Order::where('admin_view_status', 0)->get();
        if($order)
        {
            foreach($order as $orders)
                {
                    $orders->admin_view_status = 1;
                    $orders->save();
                }
        }


        $query = Order::with(['shop', 'order_tracking']);



        if ($shop_id) {
            $query->where('shop_id', $shop_id);
        }

        if ($status) {
            $query->where('delivery_status', $status);
        }

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('id',$searchValue);
            })
            ->orWhereHas('customer', function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        $data = $query->orderBy('id', 'desc')
        ->skip($start)
        ->take($length)
        ->get();

        return response()->json(['data' => $data]);
    }


    public function detail($id)
    {
        $charge = '';
        $risk = '';

        $data = Order::with(
            'order_detail.products.product_gallery',
            'order_detail.products.category',
            'order_detail.products.sub_category',
            'order_detail.products.brand',
            'order_detail.products.model',
            'order_detail.products.stock',
            'order_detail.varient',
            'order_detail.products.reviews.user',
            'order_detail.products.tax',
            'order_detail.products.shop.shop_policy',
            'order_status',
            'order_tracking',
            'order_refund',
            'shop',
            'nagative_payout_balance',
            'coupon_user.coupon',
            'order_timeline'
        )->where('id', $id)->first();

            if($data->view_status == 0)
            {
                $data->view_status = 1;
                $data->save();
            }


        // Stripe Payment Details
        if ($data->payment_method == 'STRIPE') {
            try {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $paymentIntent = PaymentIntent::retrieve($data->stripe_payment_id);
                $charge = Charge::retrieve($paymentIntent->latest_charge);

            } catch (\Exception $e) {
                // Log the error or just continue silently
                $charge = ''; // Optional: set to null or leave empty
            }
        }

        // PayPal Payment Details
        if ($data->payment_method == 'PAYPAL') {
            try {
                $clientId = config('services.paypal.client_id');
                $clientSecret = config('services.paypal.secret');
                $client = new \GuzzleHttp\Client();

                $tokenResponse = $client->post("https://api-m.paypal.com/v1/oauth2/token", [
                    'auth' => [$clientId, $clientSecret],
                    'form_params' => ['grant_type' => 'client_credentials'],
                ]);

                $accessToken = json_decode($tokenResponse->getBody(), true)['access_token'];

                $paymentResponse = Http::withToken($accessToken)->get("https://api-m.paypal.com/v2/payments/captures/{$data->stripe_payment_id}");

                if ($paymentResponse->successful()) {
                    $paymentDetails = $paymentResponse->json();

                    $paypal_order_id = $paymentDetails['supplementary_data']['related_ids']['order_id'] ?? null;

                    if ($paypal_order_id) {
                        $paypal_order_response = Http::withToken($accessToken)->get("https://api-m.paypal.com/v2/checkout/orders/{$paypal_order_id}");
                        $risk = $paypal_order_response->json();
                    }
                }

            } catch (\Exception $e) {
                // Log the error or just continue silently
                $risk = ''; // Optional: set to null or leave empty
            }
        }

        return response()->json([
            'data' => $data,
            'charge' => $charge,
            'risk' => $risk,
        ]);
    }



    public function delivery_status(Request $request)
    {
        $order = Order::where('id',$request->id)->first();

        $user = User::where('id',$order->customer_id)->first();

        if($request->delivery_status == 'Confirmed')
        {

            $OrderStatus = new OrderStatus();
            $OrderStatus->order_id = $request->id;
            $OrderStatus->status = 'confirmed';
            $OrderStatus->save();

            Mail::send(
                'email.Order.order_confirmation',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );
        }
        elseif($request->delivery_status == 'Picked Up')
        {

            $OrderStatus = new OrderStatus();
            $OrderStatus->order_id = $request->id;
            $OrderStatus->status = 'picked up';
            $OrderStatus->save();

        }
        elseif($request->delivery_status == 'Delivered')
        {

            $OrderStatus = new OrderStatus();
            $OrderStatus->order_id = $request->id;
            $OrderStatus->status = 'deliverd';
            $OrderStatus->save();

            Mail::send(
                'email.Order.order_completed',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );

            $NewPayout = new Payout();
            $NewPayout->date = Carbon::now();
            $NewPayout->seller_id = $order->sellers_id;
            $NewPayout->order_id = $order->id;
            $NewPayout->amount = $order->amount;
            $NewPayout->payment_status = $order->payment_method;
            $NewPayout->save();

        }
        else
        {

            Mail::send(
                'email.Order.order_ontheway',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );


        }

        $order->delivery_status = $request->delivery_status;
        $order->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function update_tags(Request $request)
    {
        Order::where('id', $request->id)->update(['tags' => $request->tags]);
        $response = ['status'=>true,"message" => "tag saved successfully!"];
        return response($response, 200);
    }

    public function payment_status(Request $request)
    {
        $changeStatus = Order::where('id',$request->id)->first();
        $changeStatus->payment_status = $request->payment_status;
        $changeStatus->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function order_view()
    {
        
            $order = Order::where('admin_view_status', 0)->get();
            if($order)
            {
                foreach($order as $orders)
                    {
                        $orders->admin_view_status = 1;
                        $orders->save();
                    }
            }
            $response = ['status'=>true,"message" => "Order Viewed Successfully!"];
            return response($response, 200);
    }

    public function delete($id)
    {
        Order::find($id)->delete();

        $response = ['status'=>true,"message" => "Order Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Order::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Orders Deleted Successfully!"];
        return response($response, 200);
    }
}
