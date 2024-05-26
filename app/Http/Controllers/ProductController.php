<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ProductController extends Controller
{
    public function index()
    {
        $data = Item::with('stocks')->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => $item->price,
                    'quantity' => $item->stocks->sum('quantity'),
                    'updated_at' => $item->stocks->max('updated_at'),
                ];
            });
        return $this->successResponse($data);
    }
}
