<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product as RequestsProduct;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([

            'data' => ProductCollection::collection(Product::paginate(5))

        ], Response::HTTP_PARTIAL_CONTENT);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestsProduct $request)
    {
        if (request()->user()->isAdmin()) {
            $product = new Product;
            $product->name = $request->name;
            $product->detail = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->discount = $request->discount;
            $product->admin_id =  request()->user()->id;

            $product->save();

            return response([

                'data' => new ProductResource($product)

            ], Response::HTTP_CREATED);
        } else {
            return response([

                'message' => 'This user is unauthorized for this action'

            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if (request()->user()->isAdmin()) {
            $request['detail'] = $request->description;

            unset($request['description']);

            $product->update($request->all());

            return response([

                'data' => new ProductResource($product)

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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (request()->user()->isAdmin()) {
            $product->delete();

            return response(null, Response::HTTP_NO_CONTENT);
        } else {
            return response([

                'message' => 'This user is unauthorized for this action'

            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
