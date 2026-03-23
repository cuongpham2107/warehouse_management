<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE VIEW pallet_with_info AS
            SELECT 
                p.id AS pallet_id,
                p.crate_id,
                p.location_code,
                p.status AS pallet_status,
                p.checked_in_at,
                p.checked_in_by,
                p.checked_out_at,
                p.checked_out_by,
                p.created_at AS pallet_created_at,
                p.updated_at AS pallet_updated_at,

                -- Receiving Plan fields
                rp.plan_code,
                rp.vendor_id,
                rp.license_plate AS receiving_license_plate,
                rp.transport_garage AS receiving_transport_garage,
                rp.vehicle_capacity AS receiving_vehicle_capacity,
                rp.plan_date,
                rp.arrival_date,
                rp.notes AS receiving_notes,

                -- Crate fields
                c.id AS crate_id_real,
                c.description AS crate_description,
                c.pcs AS crate_pcs,
                c.gross_weight AS crate_gross_weight,
                c.dimensions_length AS crate_length,
                c.dimensions_width AS crate_width,
                c.dimensions_height AS crate_height,
                c.receiving_plan_id,

                -- Shipping Request fields
                sr.id AS shipping_request_id,
                sr.request_code,
                sr.customer_name,
                sr.requested_date,
                sr.lifting_time,
                sr.transport_garage AS shipping_transport_garage,
                sr.vehicle_capacity AS shipping_vehicle_capacity,
                sr.departure_time,
                sr.license_plate AS shipping_license_plate,
                sr.driver_name,
                sr.notes AS shipping_notes

            FROM pallets p
            LEFT JOIN crates c 
                ON c.id = p.crate_id
            LEFT JOIN receiving_plans rp 
                ON rp.id = c.receiving_plan_id
            LEFT JOIN shipping_request_items sri 
                ON sri.crate_id = c.id
            LEFT JOIN shipping_requests sr 
                ON sr.id = sri.shipping_request_id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS pallet_with_info");
    }
};
