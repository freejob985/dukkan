<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Category;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PaytmController;
use App\Order;
use App\BusinessSetting;
use App\Coupon;
use App\CouponUsage;
use App\User;
use App\Address;
use App\AffiliateOption;
use App\AffiliateUser;
 
use App\Orderbalance;
use App\Sendcart;
use App\Sendcartuser;
use Session;
use Mail;
use App\Mail\Cartemail;
use App\Utility\PayhereUtility;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly

    public function checkout(Request $request)
    {



        if ($request->payment_option != null) {

            $orderController = new OrderController;

            $orderController->store($request);

            $request->session()->put('payment_type', 'cart_payment');

            $token = session()->get('token');

            $sender = session()->get('sender_id');


            if($request->session()->get('order_id') != null){

                if($request->payment_option == 'paypal'){
                    $paypal = new PaypalController;
                    return $paypal->getCheckout();
                }
                elseif ($request->payment_option == 'stripe') {
                    $stripe = new StripePaymentController;
                    return $stripe->stripe();
                }
                elseif ($request->payment_option == 'sslcommerz') {
                    $sslcommerz = new PublicSslCommerzPaymentController;
                    return $sslcommerz->index($request);
                }
                elseif ($request->payment_option == 'instamojo') {
                    $instamojo = new InstamojoController;
                    return $instamojo->pay($request);
                }
                elseif ($request->payment_option == 'razorpay') {
                    $razorpay = new RazorpayController;
                    return $razorpay->payWithRazorpay($request);
                }
                elseif ($request->payment_option == 'paystack') {
                    $paystack = new PaystackController;
                    return $paystack->redirectToGateway($request);
                }
                elseif ($request->payment_option == 'voguepay') {
                    $voguePay = new VoguePayController;
                    return $voguePay->customer_showForm();
                }
                elseif ($request->payment_option == 'twocheckout') {
                    $twocheckout = new TwoCheckoutController;
                    return $twocheckout->index($request);
                }
                elseif ($request->payment_option == 'payhere') {
                    $order = Order::findOrFail($request->session()->get('order_id'));

                    $order_id = $order->id;
                    $amount = $order->grand_total;
                    $first_name = json_decode($order->shipping_address)->name;
                    $last_name = 'X';
                    $phone = json_decode($order->shipping_address)->phone;
                    $email = json_decode($order->shipping_address)->email;
                    $address = json_decode($order->shipping_address)->address;
                    $city = json_decode($order->shipping_address)->city;

                    return PayhereUtility::create_checkout_form($order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
                }
                elseif ($request->payment_option == 'paytm') {
                    $paytm = new PaytmController;
                    return $paytm->index();
                }
                elseif ($request->payment_option == 'cash_on_delivery') {
                    $request->session()->put('cart', collect([]));
                    // $request->session()->forget('order_id');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');


                    flash(translate("Your order has been placed successfully"))->success();

                	return redirect()->route('order_confirmed');
                }
                elseif ($request->payment_option == 'wallet') {
                    $user = Auth::user();
                    $user->balance -= Order::findOrFail($request->session()->get('order_id'))->grand_total;
                    $user->save();
                    return $this->checkout_done($request->session()->get('order_id'), null);
                }
                else{
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    $order->manual_payment = 1;
                    $order->save();

                    $request->session()->put('cart', collect([]));
                    // $request->session()->forget('order_id');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');

                    flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                	return redirect()->route('order_confirmed');
                }
            }
        }else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            $clubpointController = new ClubPointController;
            $clubpointController->processClubPoints($order);
        }

        if(\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated){
            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if($orderDetail->product->user->user_type == 'seller'){
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
            else{
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if($orderDetail->product->user->user_type == 'seller'){
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price*(100-$commission_percentage))/100  + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        }
        else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if($orderDetail->product->user->user_type == 'seller'){
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();

        Session::put('cart', collect([]));
        // Session::forget('order_id');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');

        dd(Session::get('token'));

        if (session()->has('token')){

            $order->token = Session::get('token');
            $order->save();

         }



        flash(translate('Payment completed'))->success();

        return view('frontend.order_confirmed', compact('order'));
    }

    public function get_shipping_info(Request $request)
    {
        if(Session::has('cart') && count(Session::get('cart')) > 0){
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if (Auth::check()) {
            if($request->address_id == null){
                flash(translate("Please add shipping address"))->warning();
                return back();
            }
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        }
        else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }

        $shipping_info = $data;
        $request->session()->put('shipping_info', $shipping_info);

        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem){
            $subtotal += $cartItem['price']*$cartItem['quantity'];
            $tax += $cartItem['tax']*$cartItem['quantity'];
            $shipping += $cartItem['shipping']*$cartItem['quantity'];
        }

        $total = $subtotal + $tax + $shipping;

        if(Session::has('coupon_discount')){
                $total -= Session::get('coupon_discount');
        }

        return view('frontend.delivery_info');
        // return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {

        if(Session::has('cart') && count(Session::get('cart')) > 0){
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                if(\App\Product::find($object['id'])->added_by == 'admin'){
                    if($request['shipping_type_admin'] == 'home_delivery'){
                        $object['shipping_type'] = 'home_delivery';
                    }
                    else{
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request->pickup_point_id_admin;
                    }
                }
                else{
                    if($request['shipping_type_'.\App\Product::find($object['id'])->user_id] == 'home_delivery'){
                        $object['shipping_type'] = 'home_delivery';
                    }
                    else{
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request['pickup_point_id_'.\App\Product::find($object['id'])->user_id];
                    }
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            $cart = $cart->map(function ($object, $key) use ($request) {
                $object['shipping'] = getShippingCost($key);
                return $object;
            });

            $request->session()->put('cart', $cart);

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem){
                $subtotal += $cartItem['price']*$cartItem['quantity'];
                $tax += $cartItem['tax']*$cartItem['quantity'];
                $shipping += $cartItem['shipping']*$cartItem['quantity'];
            }

            $total = $subtotal + $tax + $shipping;

            if(Session::has('coupon_discount')){
                    $total -= Session::get('coupon_discount');
            }

            //dd($total);

            return view('frontend.payment_select', compact('total'));
        }
        else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function get_payment_info(Request $request)
    {
        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem){
            $subtotal += $cartItem['price']*$cartItem['quantity'];
            $tax += $cartItem['tax']*$cartItem['quantity'];
            $shipping += $cartItem['shipping']*$cartItem['quantity'];
        }

        $total = $subtotal + $tax + $shipping;

        if(Session::has('coupon_discount')){
                $total -= Session::get('coupon_discount');
        }

        return view('frontend.payment_select', compact('total'));
    }

    public function apply_coupon_code(Request $request){
        //dd($request->all());
        $coupon = Coupon::where('code', $request->code)->first();

        if($coupon != null){
            if(strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date){
                if(CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null){
                    $coupon_details = json_decode($coupon->details);

                    if ($coupon->type == 'cart_base')
                    {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;

                        foreach (Session::get('cart') as $key => $cartItem)
                        {
                            $subtotal += $cartItem['price']*$cartItem['quantity'];
                            $tax += $cartItem['tax']*$cartItem['quantity'];
                            $shipping += $cartItem['shipping']*$cartItem['quantity'];
                        }
                        $sum = $subtotal+$tax+$shipping;

                        if ($sum > $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount =  ($sum * $coupon->discount)/100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            }
                            elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                            $request->session()->put('coupon_id', $coupon->id);
                            $request->session()->put('coupon_discount', $coupon_discount);
                            flash(translate('Coupon has been applied'))->success();
                        }
                    }
                    elseif ($coupon->type == 'product_base')
                    {
                        $coupon_discount = 0;
                        foreach (Session::get('cart') as $key => $cartItem){
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if($coupon_detail->product_id == $cartItem['id']){
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += $cartItem['price']*$coupon->discount/100;
                                    }
                                    elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount;
                                    }
                                }
                            }
                        }
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_discount', $coupon_discount);
                        flash(translate('Coupon has been applied'))->success();
                    }
                }
                else{
                    flash(translate('You already used this coupon!'))->warning();
                }
            }
            else{
                flash(translate('Coupon expired!'))->warning();
            }
        }
        else {
            flash(translate('Invalid coupon!'))->warning();
        }
        return back();
    }

    public function remove_coupon_code(Request $request){
        $request->session()->forget('coupon_id');
        $request->session()->forget('coupon_discount');
        return back();
    }

    public function order_confirmed(){



        $order = Order::findOrFail(Session::get('order_id'));


        if(session()->has('sender_id')){


        $option = AffiliateOption::where('type', '=', 'product_sharing')->first();

        $comisstion = $option->percentage;

        $total = $order->grand_total;

        $balance = $total / $comisstion;

        $sender_id = session()->get('sender_id');

        $sender = User::findOrFail($sender_id);

         $edited = Sendcart::where('sender_id', '=',$sender_id)->latest()->first();

         if($edited){

            if($edited->count > 0){


                $balance = $balance / 2;

            }else{


                $balance = $balance;
            }

         }


        $token = session()->get('token');

        $sendersession = session()->get('sender_id');

        $order->update(['token' => $token]);

        $isAffilate = AffiliateUser::where('user_id', '=', $sender_id)->first();


        if($order->payment_type  == 'cash_on_delivery'){
            
            $amounts = 0;

            foreach($order->orderDetails as $index=>$product){
                

                $price = $product->price * $product->quantity;
                $amounts .= $price;
            }
            


            Orderbalance::create([

                'order_id'  =>  $order->id,
                'balance'   =>  $amounts,
                'sender_id' =>  $sender->id
            ]);


        }else{

        if($isAffilate){


            $old =  AffiliateUser::where('user_id', '=',  $sender->id)->first();

            $old->increment('balance', $balance);

        }else{


           AffiliateUser::create([

            'paypal_email'     => $sender->email,
            'user_id'          => $sender->id,
            'bank_information' => '123456',
            'status'           => '1',
            'balance'           =>  $balance,
        ]);


        }

    }

    }

       Session::forget('sender_id');

       Session::forget('token');


        return view('frontend.order_confirmed', compact('order'));
    }


    public function send_to_user(Request $request) {

           if(Session::has('cart') && count(Session::get('cart')) > 0){

            $cart = $request->session()->get('cart', collect([]));


            $subtotal = 0;

            $tax = 0;


            foreach (Session::get('cart') as $key => $cartItem){

                $subtotal += $cartItem['price']*$cartItem['quantity'];

                $tax += $cartItem['tax']*$cartItem['quantity'];

             }

            $total = $subtotal + $tax;



            $sendorder = Sendcart::create([

                'sender_id' => Auth::user()->id,
                'total'     => $total,
                'qr'        => $total,
                'token'     =>   mt_rand(1000000, 9999999),
            ]);


            foreach (Session::get('cart') as $key => $cartItem){

                Sendcartuser::create([

                    'product_id' =>  $cartItem['id'],
                    'quantity'   =>  $cartItem['quantity'],
                    'price'      =>  $cartItem['price'],
                    'tax'        =>  $cartItem['tax'],
                    'digital'    =>  $cartItem['digital'],
                    'order_id'   =>  $sendorder->id,
                ]);


             }


              Session::forget('cart');


             flash(translate('Order Generated'))->success();

            return view('frontend.send_cart', compact('sendorder'));


        }else{


            return redirect('profile');
        }

}


public function getUserSendCart (Request $request, $id){


    $order = Sendcart::where('token', '=', $id)->first();

    $token = $order->token;

    if($order){


        $products = Sendcartuser::where('order_id', '=', $order->id)->get();


        $data = array();
        $cart = collect();
        $str = '';

       foreach($products as $product){

        $data['id']       = $product->product_id;
        $data['quantity'] = $product->quantity ;
        $data['price']    = $product->price;
        $data['tax']      = $product->tax;
        $data['shipping'] = 0;
        $data['product_referral_code'] = null;
        $data['digital']  = $product->digital;
        $data['variant'] = $str;

        $cart->push($data);

            }

       $request->session()->put('cart', $cart);

       $request->session()->put('sender_id', $order->sender_id);

       $request->session()->put('token', $token);

        return redirect()->route('cart');

    }else{


        flash(translate('Your Cart was empty'))->warning();

        return redirect()->route('home');
    }

    $request->session()->put('payment_type', 'cart_payment');
}


public function sendcartbyemail(Request $request) {

    $request->validate([

        'email' => 'required',
        'url'   => 'required',
    ]);

$array['view'] = 'cartemail';

$array['subject'] = 'New Order From ' . Auth::user()->name;

$array['from'] = env('MAIL_USERNAME');
$array['content'] = translate('Hi. A new order has been placed. Please Click The Link To View Your Order Or Just Copy The Link Below.');
$array['url'] = $request->url;
$array['email'] = $request->email;

 

Mail::to($array['email'])->queue(new Cartemail($array));



     flash(translate('Email Has Been Sent'))->success();

     return redirect()->back();

}
public function sendcartbywhatsapp(Request $request) {
    $request->validate(['number' => 'required','url' => 'required']);
    $url = "https://api.whatsapp.com/send/?phone=".$request->number."&text=".$request->url."&app_absent=0";
return redirect()->to($url);
}


}
