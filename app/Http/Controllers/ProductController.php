<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch products ordered by 'created_at' in descending order (newest first)
        $data = Product::orderBy('created_at', 'desc')->get();

        // Add the full image URL to each product in the response
        foreach ($data as $product) {
            // Assuming 'image' is the relative path stored in the database
            $product->image = asset('products/' . $product->image);
        }

        return response()->json(['message' => 'Success', 'data' => $data], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Ensure the 'public/products' directory exists
            if (!file_exists(public_path('products'))) {
                mkdir(public_path('products'), 0755, true);
            }

            // Handle the file upload to the public/products directory
            $imageFile = $request->file('image');

            // Get the original file name (without the extension)
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

            // Create a unique name for the file to prevent overwriting
            $imageName = time() . '_' . $originalName . '.' . $imageFile->getClientOriginalExtension();

            // Move the file to the public/products directory
            $imagePath = $imageFile->move(public_path('products'), $imageName);


            // Save the relative path to the database
            // $imagePath = 'products/' . $imageName;

            // Create the product
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'image' => $imageName,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
                "file_part" => $imagePath,
            ], 201);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        // Assuming 'image' is the relative path stored in the database
        $product->image = asset('products/' . $product->image);

        return response()->json(['message' => 'Success', 'data' => $product], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric|min:0',
                // 'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',  // Image is optional in update
            ]);

            // dd($validated);

            // Find the product by ID
            $product = Product::findOrFail($id);

            // Check if image was uploaded
            if ($request->hasFile('image')) {
                // Ensure the 'public/products' directory exists
                // if (!file_exists(public_path('products'))) {
                //     mkdir(public_path('products'), 0755, true);
                // }

                // Delete the old image if it exists
                if (file_exists(public_path('products/' . $product->image))) {
                    unlink(public_path('products/' . $product->image));
                }

                // Handle the new file upload
                // Handle the file upload to the public/products directory
                $imageFile = $request->file('image');

                // Get the original file name (without the extension)
                $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // Create a unique name for the file to prevent overwriting
                $imageName = time() . '_' . $originalName . '.' . $imageFile->getClientOriginalExtension();

                // Move the file to the public/products directory
                $imagePath = $imageFile->move(public_path('products'), $imageName);

                // Update the image field
                $product->image = $imageName;
            }

            // Update the product data
            $product->name = $validated['name'];
            $product->description = $validated['description'];
            $product->price = $validated['price'];

            // Save the updated product
            $product->save();

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product,

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
