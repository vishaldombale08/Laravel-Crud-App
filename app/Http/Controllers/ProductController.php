<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Product;


class ProductController extends Controller
{
    //This method will show products page

    public function index(){
        $products = Product::orderBy('created_at','DESC')->get();
        return view('products.list', ['products'=>$products]);
        
    }

    //This method will show create product page

    public function create(){

        return view('products.create');

    }

    //This method will store a product in db

    public function store(Request $request){
       
        $rules= [
            'name' => 'required | min:5',
            'sku'=> 'required | min:3',
            'price'=>'required|numeric'
        ];

        if($request->image != ""){
            $rules['image']='image';
        }
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('products.create')->withInput()->withErrors($validator);
        }

        //here we will insert proudct in db

        $product = new Product();
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price= $request->price;
        $product->description= $request->description;

        $product->save();

        if($request->image != ""){
            $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time(). '.'. $ext; //Unique image name

        //save image to product directory

        $image->move(public_path('uploads/products'),$imageName);

        //save image name in database

        $product->image= $imageName;
        $product->save();
        }

        // here we will store image

        return redirect()->route('products.index')->with('success', 'Product Added Successfully');

    }
// This method will update a product
    public function edit($id){
        $product = Product::findorFail($id);
        return view('products.edit',[
            'product'=>$product
        ]);
    }
// This method will update a product
    public function update($id, Request $request){
        $product = Product::findorFail($id);

        $rules= [
            'name' => 'required | min:5',
            'sku'=> 'required | min:3',
            'price'=>'required|numeric'
        ];

        if($request->image != ""){
            $rules['image']='image';
        }
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('products.edit', $product->id)->withInput()->withErrors($validator);
        }

        //here we will update proudct in db

        
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price= $request->price;
        $product->description= $request->description;

        $product->save();

        if($request->image != ""){
            // delete old image

            File::delete(public_path('uploads/products/'.$product->image));

            $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time(). '.'. $ext; //Unique image name

        //save image to product directory

        $image->move(public_path('uploads/products'),$imageName);

        //save image name in database

        $product->image= $imageName;
        $product->save();
        }

        // here we will store image

        return redirect()->route('products.index')->with('success', 'Product updated Successfully');

    }

    //This method will delete a product

    public function destroy($id){
        $product = Product::findOrFail($id);

        //delete old image

        File::delete(public_path('uploads/products/'.$product->image));

        // delete product from database

        $product->delete();

       return redirect()->route('products.index')->with('success','Product deleted successfully');

    }


}
