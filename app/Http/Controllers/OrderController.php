<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        return OrderResource::collection(Order::where('product_id', $product->id)->paginate(20));
    }

    public function show(Product $product, Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, Order $order)
    {
        $order = new Order($request->all());
        $order->product_id = $product->id;
        $order->user_id = request()->user()->id;
        $order->status = "processed";
        $product->stock -= $order->amount;
        $product->save();
        $order->save();

        return response([
            'data' => new OrderResource($order)
        ], Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        if (request()->user()->isAdmin()) {
            $order->update($request->all());

            return response([
                'data' => new OrderResource($order)
            ], Response::HTTP_CREATED);
        } else {
            return response([

                'message' => 'This user is unauthorized for this action'

            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Order $order)
    {
        if (request()->user()->isAdmin()) {
            $order->delete();
            return response(null, Response::HTTP_NO_CONTENT);
        } else {
            return response([

                'message' => 'This user is unauthorized for this action'

            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
