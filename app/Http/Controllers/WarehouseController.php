<?php

namespace App\Http\Controllers;

use App\Models\ItemStock;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->successResponse(Warehouse::all());
    }

    public function availableProducts($name): JsonResponse
    {
        $itemStock = ItemStock::with('item')
            ->whereHas('warehouse', function ($query) use ($name) {
                $query->where('name', $name);
            })
            ->get()->map(function ($item) {
                return [
                    'id' => $item->item->id,
                    'name' => $item->item->name,
                ];
            });
        return $this->successResponse($itemStock);
    }
}
