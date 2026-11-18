<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BasicRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Quản lý kho (warehouse_manager) - Toàn quyền vận hành nghiệp vụ kho, không chỉnh sửa User & Role hệ thống
        $warehouseManager = Role::firstOrCreate(['name' => 'warehouse_manager', 'guard_name' => 'web']);
        $warehouseManager->syncPermissions([
            // Kế hoạch nhận hàng
            'view_any_receiving::plans::receiving::plan',
            'view_receiving::plans::receiving::plan',
            'create_receiving::plans::receiving::plan',
            'update_receiving::plans::receiving::plan',
            'delete_receiving::plans::receiving::plan',
            'delete_any_receiving::plans::receiving::plan',

            // Yêu cầu xuất hàng
            'view_any_shipping::requests::shipping::request',
            'view_shipping::requests::shipping::request',
            'create_shipping::requests::shipping::request',
            'update_shipping::requests::shipping::request',
            'delete_shipping::requests::shipping::request',
            'delete_any_shipping::requests::shipping::request',

            // Pallet
            'view_any_pallets::pallet',
            'view_pallets::pallet',
            'create_pallets::pallet',
            'update_pallets::pallet',
            'delete_pallets::pallet',
            'delete_any_pallets::pallet',

            // Kiện hàng
            'view_any_crates::crate',
            'view_crates::crate',
            'create_crates::crate',
            'update_crates::crate',
            'delete_crates::crate',
            'delete_any_crates::crate',

            // Vị trí kho
            'view_any_warehouse::locations::warehouse::location',
            'view_warehouse::locations::warehouse::location',
            'create_warehouse::locations::warehouse::location',
            'update_warehouse::locations::warehouse::location',
            'delete_warehouse::locations::warehouse::location',
            'delete_any_warehouse::locations::warehouse::location',

            // Nhà cung cấp
            'view_any_vendors::vendor',
            'view_vendors::vendor',
            'create_vendors::vendor',
            'update_vendors::vendor',

            // Thiết bị
            'view_any_devices::device',
            'view_devices::device',
            'create_devices::device',
            'update_devices::device',

            // Lịch sử di chuyển & nhật ký
            'view_any_inventory::movements::inventory::movement',
            'view_inventory::movements::inventory::movement',
            'view_any_audit::logs::audit::log',
            'view_audit::logs::audit::log',

            // Xem danh sách người dùng
            'view_any_users::user',
            'view_users::user',

            // Trang & Widgets
            'page_ReportWarehouse',
            'page_PalletActivity',
            'widget_EmployeeKPIWidget',
            'widget_PalletCountByStatusWidget',
            'widget_PalletStatsWidget',
            'widget_ReceivingPlanStatsWidget',
            'widget_RecentShippingRequestsWidget',
            'widget_ShippedPalletsWidget',
            'widget_StatsOverviewWidget',
            'widget_WarehouseActivityWidget',
        ]);

        // 2. Nhân viên nhập hàng / Kiểm đếm (checker) - Chuyên tiếp nhận, kiểm đếm và gắn pallet
        $checker = Role::firstOrCreate(['name' => 'checker', 'guard_name' => 'web']);
        $checker->syncPermissions([
            // Nhận hàng
            'view_any_receiving::plans::receiving::plan',
            'view_receiving::plans::receiving::plan',
            'create_receiving::plans::receiving::plan',
            'update_receiving::plans::receiving::plan',

            // Kiện hàng
            'view_any_crates::crate',
            'view_crates::crate',
            'create_crates::crate',
            'update_crates::crate',

            // Pallet
            'view_any_pallets::pallet',
            'view_pallets::pallet',
            'create_pallets::pallet',
            'update_pallets::pallet',

            // Vị trí kho & Nhà cung cấp
            'view_any_warehouse::locations::warehouse::location',
            'view_warehouse::locations::warehouse::location',
            'view_any_vendors::vendor',
            'view_vendors::vendor',

            // Trang & Widgets
            'page_PalletActivity',
            'widget_ReceivingPlanStatsWidget',
            'widget_PalletStatsWidget',
            'widget_PalletCountByStatusWidget',
            'widget_StatsOverviewWidget',
        ]);

        // 3. Nhân viên xuất hàng (shipping_staff) - Chuyên soạn hàng và xuất kho
        $shippingStaff = Role::firstOrCreate(['name' => 'shipping_staff', 'guard_name' => 'web']);
        $shippingStaff->syncPermissions([
            // Yêu cầu xuất hàng
            'view_any_shipping::requests::shipping::request',
            'view_shipping::requests::shipping::request',
            'create_shipping::requests::shipping::request',
            'update_shipping::requests::shipping::request',

            // Pallet & Kiện
            'view_any_pallets::pallet',
            'view_pallets::pallet',
            'update_pallets::pallet',
            'view_any_crates::crate',
            'view_crates::crate',

            // Vị trí kho
            'view_any_warehouse::locations::warehouse::location',
            'view_warehouse::locations::warehouse::location',

            // Trang & Widgets
            'page_PalletActivity',
            'widget_RecentShippingRequestsWidget',
            'widget_ShippedPalletsWidget',
            'widget_StatsOverviewWidget',
        ]);

        // 4. Lái xe nâng / Nhân viên vị trí (forklift_operator) - Cất / hạ / di chuyển vị trí pallet
        $forklift = Role::firstOrCreate(['name' => 'forklift_operator', 'guard_name' => 'web']);
        $forklift->syncPermissions([
            // Pallet
            'view_any_pallets::pallet',
            'view_pallets::pallet',
            'update_pallets::pallet',

            // Vị trí kho & Kiện hàng
            'view_any_warehouse::locations::warehouse::location',
            'view_warehouse::locations::warehouse::location',
            'view_any_crates::crate',
            'view_crates::crate',

            // Trang & Widgets
            'page_PalletActivity',
            'widget_PalletStatsWidget',
            'widget_WarehouseActivityWidget',
        ]);

        // 5. Nhân viên tra cứu / Báo cáo (viewer) - Chỉ xem dữ liệu, không sửa đổi
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view_any_receiving::plans::receiving::plan',
            'view_receiving::plans::receiving::plan',
            'view_any_shipping::requests::shipping::request',
            'view_shipping::requests::shipping::request',
            'view_any_crates::crate',
            'view_crates::crate',
            'view_any_pallets::pallet',
            'view_pallets::pallet',
            'view_any_warehouse::locations::warehouse::location',
            'view_warehouse::locations::warehouse::location',
            'view_any_vendors::vendor',
            'view_vendors::vendor',
            'view_any_inventory::movements::inventory::movement',
            'view_inventory::movements::inventory::movement',

            // Trang & Widgets
            'page_ReportWarehouse',
            'page_PalletActivity',
            'widget_StatsOverviewWidget',
            'widget_PalletStatsWidget',
            'widget_ReceivingPlanStatsWidget',
            'widget_RecentShippingRequestsWidget',
            'widget_ShippedPalletsWidget',
            'widget_PalletCountByStatusWidget',
            'widget_WarehouseActivityWidget',
        ]);
    }
}
