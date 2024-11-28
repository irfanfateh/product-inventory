<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    private $filePath = 'storage/products.json';

    public function index()
    {
        $data = $this->getJsonData();
        return view('products', ['products' => $data]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric',
        ]);
        $data['total_value'] = $data['quantity_in_stock'] * $data['price_per_item'];
        $data['datetime_submitted'] = now();
        $jsonData = $this->getJsonData();
        $jsonData[] = $data;
        $this->storeJsonData($jsonData);
        return response()->json([
            'success' => true,
            'index' => count($jsonData) - 1,
            'product' => $data,
        ]);
    }

    public function update(Request $request, $index)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity_in_stock' => 'required|integer',
            'price_per_item' => 'required|numeric',
        ]);
        $jsonData = $this->getJsonData();
        if (!isset($jsonData[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $data['total_value'] = $data['quantity_in_stock'] * $data['price_per_item'];
        $data['datetime_submitted'] = $jsonData[$index]['datetime_submitted'];
        $jsonData[$index] = $data;
        $this->storeJsonData($jsonData);
        return response()->json([
            'success' => true,
            'index' => $index,
            'product' => $data,
        ]);
    }

    private function getJsonData()
    {
        if (!File::exists($this->filePath)) {
            return [];
        }
        try {
            $json = File::get($this->filePath);
            return json_decode($json, true) ?? [];
        } catch (\Exception $e) {
            var_dump($e);
            die();
        }
    }

    private function storeJsonData($data)
    {
        try {
            File::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            var_dump($e);
            die();
        }
    }
}
