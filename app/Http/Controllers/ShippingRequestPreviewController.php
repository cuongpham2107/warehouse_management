<?php

namespace App\Http\Controllers;

use App\Models\ShippingRequest;
use Illuminate\Http\Request;

class ShippingRequestPreviewController extends Controller
{
    public function show($id)
    {
        $shippingRequest = ShippingRequest::with([
            'creator',
            'items.crate',
            'items.crate.pallet'
        ])->findOrFail($id);

        // Tính tổng
        $totalPcs = 0;
        $totalGrossWeight = 0;
        $totalPieces = 0;
        foreach ($shippingRequest->items as $item) {
            $totalPcs += $item->crate->pcs ?? 0;
            $totalGrossWeight += $item->crate->gross_weight ?? 0;
            $totalPieces += $item->crate->pieces ?? 0;
        }

        return view('exports.invoices', [
            'shippingRequest' => $shippingRequest,
            'totalPcs' => $totalPcs,
            'totalGrossWeight' => $totalGrossWeight,
            'totalPieces' => $totalPieces,
            'preview' => true, // Thêm biến này để xác định là xem trước
        ]);
    }
}
