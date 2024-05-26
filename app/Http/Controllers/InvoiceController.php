<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Item;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function index(Request $request): JsonResponse
    {

        $limit = $request->limit ?? 10;

        $invoices = Invoice::with(['customer:id,name', 'details:invoice_id,total'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        $invoices->setCollection(
            $invoices->getCollection()->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'status' => $invoice->status,
                    'sales_order_number' => $invoice->invoice_number,
                    'so_date' => Carbon::parse($invoice->invoice_date)->format('Y-m-d'),
                    'customer_name' => $invoice->customer->name,
                    'sales_person' => $invoice->sales_person,
                    'terms' => $invoice->terms,
                    'net_total' => $invoice->details->sum('total'),
                    'remarks' => $invoice->remarks,
                ];
            }));

        return $this->successResponse(self::autoPaginateWrapper($invoices));
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {

        $invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'invoice_date' => $request->invoice_date,
            'invoice_number' => $this->generateInvoiceNumber($request->invoice_date, $request->warehouse_name, $request->order_type),
            'sales_person' => $request->sales_person,
            'created_by' => auth()->user()->name,
            'status' => $request->status,
            'terms' => $request->terms,
            'remarks' => $request->remarks,
            'discount' => $request->discount,
        ]);

        foreach ($request->items as $item) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'item_id' => $item['item_id'],
                'warehouse_id' => Warehouse::where('name', $request->warehouse_name)->first()->id,
                'uom_id' => Item::find($item['item_id'])->uom_id,
                'quantity' => $item['quantity'],
                'price' => Item::find($item['item_id'])->price,
                'is_return' => $request->is_return,
                'total' => ($item['quantity'] * Item::find($item['item_id'])->price)
            ]);
        }
        return $this->successResponse();
    }

    private function generateInvoiceNumber($invoiceDate, $warehouseName, $orderType): string
    {
        // return eg: SI/MDN/2405.0001
        $date = Carbon::parse($invoiceDate)->format('ym');
        $lastInvoice = Invoice::where('invoice_number', 'like', "%$warehouseName%")->latest()->value('invoice_number') ?? "$orderType/$warehouseName/$date.0000";
        $lastCount = explode('.', $lastInvoice)[1];

        // make eg: 0001
        $newCount = str_pad((int)$lastCount + 1, 4, '0', STR_PAD_LEFT);
        return "$orderType/$warehouseName/$date.$newCount";
    }

    public function show($salesOrderNumber): JsonResponse
    {
        $invoiceNumber = str_replace('_', '/', $salesOrderNumber);
        $invoiceData = Invoice::with(['customer', 'details', 'details.item', 'details.warehouse', 'details.uom'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (!$invoiceData) {
            return $this->errorResponse('Invoice not found', 404);
        }

        $itemData = $invoiceData->details->map(function ($item) {
            return [
                'item_code' => $item->item->code,
                'item_name' => $item->item->name,
                'warehouse' => $item->warehouse->name,
                'quantity' => $item->quantity,
                'uom' => $item->uom->name,
                'price' => $item->price,
                'discount' => "",
                'total' => $item->total,
            ];
        });

        $data = [
            'status' => $invoiceData->status,
            'date' => $invoiceData->invoice_date,
            'return' => $invoiceData->is_return ? 'Yes' : 'No',
            'customer_name' => $invoiceData->customer->name,
            'sales_person' => $invoiceData->sales_person,
            'created_by' => $invoiceData->created_by,
            'remarks' => $invoiceData->remarks ?? '',
            'total_quantity' => $invoiceData->details->sum('quantity'),
            'total_discount' => $invoiceData->discount,
            'total_amount' => $invoiceData->details->sum('total'),
            'total_net' => $invoiceData->details->sum('total') - $invoiceData->discount,
            'items' => $itemData,
        ];

        return $this->successResponse($data);
    }

    public function update(UpdateStatusRequest $request, $salesOrderNumber): JsonResponse
    {
        $invoiceNumber = str_replace('_', '/', $salesOrderNumber);
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        if (!$invoice) {
            return $this->failResponse('Invoice not found', 404);
        }

        $invoice->update([
            'status' => $request->status,
        ]);

        return $this->successResponse();
    }

    public function destroy($salesOrderNumber): JsonResponse
    {
        $invoiceNumber = str_replace('_', '/', $salesOrderNumber);
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();

        if (!$invoice) {
            return $this->failResponse('Invoice not found', 404);
        }

        $invoice->details()->delete();
        $invoice->delete();

        return $this->successResponse();
    }
}
