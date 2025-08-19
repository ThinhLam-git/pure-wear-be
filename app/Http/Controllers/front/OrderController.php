<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function saveOrder(Request $request)
    {
        if (!empty($request->cart)) {
            $order = new Order();

            $order->name = $request->name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->address = $request->address;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->user_id = $request->user()->id;
            $order->subtotal = $request->subtotal;
            $order->grand_total = $request->grand_total;
            $order->shipping = $request->shipping;
            $order->discount = $request->discount;
            $order->payment_status = $request->payment_status;
            $order->order_status = $request->order_status;
            $order->save();

            foreach ($request->cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->name = $item['name'];
                $orderItem->size = $item['size'];
                $orderItem->qty = $item['qty'];
                $orderItem->unit_price = $item['price'];
                $orderItem->price = $item['qty'] * $item['price'];
                $orderItem->save();
            }

            return response()->json(['status' => 200, 'message' => 'You have placed your order successfully'], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'Your cart is empty']);
        }
    }
}
