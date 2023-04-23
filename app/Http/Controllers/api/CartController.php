<?php

namespace App\Http\Controllers\api;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartController extends Controller
{
    public function getCartByIdUser($idUser)
    {
        try {
            $user = User::findOrFail($idUser);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $cart = Cart::where('id_user', $idUser)->first();
        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }
        return response()->json(['cartData' => $cart->cartData], 200);
    }
    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'dataCart' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        try {
            $user = User::findOrFail($request->id_user);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // Find the cart for the user
        $cart = Cart::where('id_user', $user->id)->firstOrFail();

        // Update the cart data
        $cart->dataCart = $request->dataCart;
        $cart->save();

        // Return a success response
        return response()->json(['success' => true], 200);
    }
}
