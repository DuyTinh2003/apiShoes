<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('orderDetails.product')
            ->join('users', 'orders.id_user', '=', 'users.id')
            ->select('orders.id_user', 'orders.id', 'users.name', 'orders.phone', 'orders.address', 'orders.status')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'order_details' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $order = new Order;
        $order->id_user = $request->id_user;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->status = "pending";
        $order->save();
        if ($request->order_details) {
            foreach ($request->order_details as $key => $val) {
                $orderDetail = new OrderDetail();
                $orderDetail->id_order = $order->id;
                $orderDetail->id_product = $val['id_product'];
                $orderDetail->amount = $val['amount'];
                $orderDetail->price = $val['price'];
                $orderDetail->size = $val['size'];
                $orderDetail->save();
            }
        };
        return response()->json($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getOrderByIdUser($id_user)
    {
        $user = User::find($id_user);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }
        // $orders = Order::with(['orderDetails.product'])
        //     ->where('id_user', $id_user)
        //     ->get();
        $order = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.id_user')
            ->join('order_details', 'order_details.id_order', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_details.id_product')
            ->select('orders.id', 'orders.status', 'order_details.amount', 'order_details.price', 'products.name', 'products.image', 'products.type', 'products.description', 'products.sale', 'orders.created_at', 'orders.updated_at')
            ->where('orders.id_user', $id_user)
            // ->orderBy('orders.id', 'desc')
            ->get();
        return response()->json(["data" => $order]);
    }
}
