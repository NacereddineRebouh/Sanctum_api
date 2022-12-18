<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list()
    {
        return Product::all();
    }

    public function getProduct($id)
    {
        $result = Product::find($id);
        if ($result) {
            return response()->json(array('status' => 200, 'message' => $result));
        } else {
            return  response()->json(array('status' => 404, 'message' => 'Not-found'));
        }
    }

    public function addProduct(Request $req)
    {
        $req->validate([
            'name'=>'required',
            'price'=>'required'
        ]);
        $product = new Product();
        $product->name = $req->input('name');
        $product->price = $req->input('price');
        // product_image

        $product->save();
        return response()->json(array('status' => 'success','message' => $product));
    }

    public function delete($id)
    {
        $result = Product::where('id', $id)->delete();
        if ($result) {
            return ["message" => "Product has been deleted successfully"];
        } else {
            return ["message" => "No record with the given Id"];
        }
    }
}
