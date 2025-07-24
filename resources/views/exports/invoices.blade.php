@php
$items = $items ?? $shippingRequest->items;
@endphp

<div
    style="width: 250mm; min-height: 297mm; margin: 0 auto; padding: 20px; box-sizing: border-box; font-family: Arial, sans-serif; font-size: 12px; background: white;">
    <table style="width: 100%; border-collapse: collapse; table-layout: fixed; font-family: Arial, sans-serif;">
        <!-- Logo và Header (Rows 1-3) -->
        <tr>
            <td rowspan="3" colspan="3"
                style="border: 1px solid black; width: 25%; vertical-align: top; padding: 5px; font-family: Arial, sans-serif; font-weight: 400;">
                <!-- Logo space -->
                @if(isset($preview) && $preview == true)
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 100%; height: auto;">
                @endif
            </td>
            <td rowspan="3" colspan="7"
                style="border: 1px solid black; text-align: center; vertical-align: middle; padding: 10px; width: 75%; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-size: 14px; font-family: Arial, sans-serif; font-weight: 700;">BIÊN BẢN BÀN GIAO HÀNG
                    HÓA</span><br>
                <span
                    style="font-size: 11px; font-family: Arial, sans-serif; font-style: normal; color: #666666; font-weight: 400;">PROOF
                    OF DELIVERY</span>
            </td>
        </tr>
        <tr></tr>
        <tr></tr>

        <!-- Dòng trống (Row 4) -->
        <tr>
            <td colspan="12" style="border: none; padding: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>

        <!-- Thông tin chi tiết (Rows 5-11) -->
        <tr>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: bold;">Tháng/ngày/năm:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(dd/mm/yyyy)</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->requested_date->format('d/m/Y') }}</td>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Số biên bản:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">POD
                    No.:</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->request_code }}</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Biển số xe:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Truck
                    No.)</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->license_plate }}</td>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Số niêm phong:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Seal
                    No.:</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->seal_number }}</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">TG xuất phát tại ASGL:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Time
                    of depart at ASGL):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->departure_time->format('H:i d/m/Y') }}</td>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">TG bắt đầu dỡ hàng:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Time
                    of begin unload):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">TG xe đến:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Time
                    of Arrival):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
            </td>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">TG kết thúc dỡ hàng:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Time
                    of Finish unload):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Tổng số Pallet:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Total
                    number of pallets):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                5</td>
            <td colspan="2" rowspan="2"
                style="   vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Địa điểm giao hàng:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Warehouse):</span>
            </td>
            <td colspan="3" rowspan="2"
                style="  vertical-align: middle; font-family: Arial, sans-serif; font-weight: 400;padding-left: 20px;">
                {{ $shippingRequest->delivery_address ?? '' }}</td>
        </tr>
        <tr></tr>

        <!-- Dòng trống (Row 12-13) -->
        <tr>
            <td colspan="10" style="border: none; height: 10px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 10px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>

        <!-- Header bảng dữ liệu (Rows 14-15) -->
        <tr>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                STT</th>
            <th rowspan="2"
                colspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Crate_ID</th>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Số kiện<br><span style="font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(PCS)</span></th>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Trọng lượng<br><span style="font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Gr.Weight)</span></th>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Pallet</th>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Carton<br><span style="font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(CTN)</span></th>
            <th rowspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Số lượng<br><span style="font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Q.ty)</span></th>
            <th rowspan="2" colspan="2"
                style="border: 1px solid black; padding: 5px; background-color: #e0e0e0; text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                Ghi chú<br><span style="font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">(Remark)</span></th>
        </tr>
        <tr></tr> <!-- Row 15 để tạo merge -->

        <!-- Dữ liệu bảng mẫu -->
        @foreach ($items as $i => $item)
            <tr>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $i + 1 }}</td>
                <td
                    colspan="2"
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $item->crate ? $item->crate->crate_id : '' }}</td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $item->crate->pcs ?? '' }}</td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $item->crate->gross_weight ?? '' }}</td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td colspan="2"
                    style="border: 1px solid black; padding: 5px; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $item->notes ?? '' }}</td>
            </tr>
        @endforeach

        @for ($i = count($items); $i < 20; $i++)
            <tr>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                    {{ $i + 1 }}</td>
                <td
                    colspan="2"
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td
                    style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
                <td colspan="2"
                    style="border: 1px solid black; padding: 5px; font-family: Arial, sans-serif; font-weight: 400;">
                </td>
            </tr>
        @endfor

        <!-- Dòng TOTAL -->
        <tr style="font-family: Arial, sans-serif; font-weight: 700;">
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
            </td>
            <td
                colspan="2"
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
                TOTAL</td>
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
                {{ $totalPcs }}</td>
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
                {{ $totalGrossWeight }}</td>
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
            </td>
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
            </td>
            <td
                style="border: 1px solid black; padding: 5px; text-align: center; font-family: Arial, sans-serif; font-weight: 700;">
            </td>
            <td colspan="2"
                style="border: 1px solid black; padding: 5px; font-family: Arial, sans-serif; font-weight: 700;"></td>
        </tr>


        <!-- Phần ký tên DELIVERER -->
        <tr>
            <td colspan="6"
                style="border: 1px solid black; padding: 10px;  text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                DELIVERER</td>
            <td rowspan="4" colspan="4"
                style="border: 1px solid black; padding: 10px; vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Nhân viên khai thác ASGL</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Operation
                    staff:</span><br>
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Họ tên và chữ ký:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Name
                    and signature:...............</span>
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; text-align: left; padding: 5px; padding-top: 5px; padding-left: 5px; font-family: Arial, sans-serif; font-weight: 700;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">CÔNG TY CỔ PHẦN LOGISTICS ASGL</span>
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; text-align: left; padding: 0; padding-left: 5px; font-family: Arial, sans-serif; font-weight: 400;">
                Lô số 5, KCN Yên Bình, Phường Vạn Xuân, Tỉnh Thái Nguyên, Việt Nam
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>

        <!-- Phần ký tên Driver -->
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
            <td rowspan="4" colspan="4"
                style="border: 1px solid black; padding: 10px; vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Nhân viên lái xe tải</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Driver:
                    {{ $shippingRequest->driver_name }}</span><br>
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Họ tên và chữ ký:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Name
                    and signature:...............</span>
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>

        <!-- Phần ký tên RECEIVER -->
        <tr>
            <td colspan="6"
                style="border: 1px solid black; padding: 10px;  text-align: center; vertical-align: middle; font-family: Arial, sans-serif; font-weight: 700;">
                RECEIVER</td>
            <td rowspan="4" colspan="4"
                style="border: 1px solid black; padding: 10px; vertical-align: top; font-family: Arial, sans-serif; font-weight: 400;">
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Nhân viên nhận hàng</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Receiver
                    staff:</span><br>
                <span style="font-family: Arial, sans-serif; font-weight: 700;">Họ tên và chữ ký:</span><br>
                <span
                    style="font-family: Arial, sans-serif; font-style: italic; font-size: 12px; color: #666666; font-weight: 400;">Name
                    and signature:...............</span>
            </td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; text-align: left; padding: 0; padding-left: 5px; font-family: Arial, sans-serif; font-weight: 700;">
                {{ $shippingRequest->customer_name }}</td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; text-align: left; padding: 0; padding-left: 5px; font-family: Arial, sans-serif; font-weight: 400;">
                {{ $shippingRequest->delivery_address }}</td>
        </tr>
        <tr>
            <td colspan="6"
                style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black; height: 20px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>

        <!-- Các dòng trống cuối -->
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
        <tr>
            <td colspan="10" style="border: none; height: 15px; font-family: Arial, sans-serif; font-weight: 400;">
            </td>
        </tr>
    </table>

    
</div>
