<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip stored procedures and triggers for SQLite
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Create function to calculate receiving plan totals (MySQL only)
        DB::unprepared('
            CREATE FUNCTION calculate_plan_totals(plan_id BIGINT)
            RETURNS JSON
            READS SQL DATA
            BEGIN
                DECLARE result JSON;
                SELECT JSON_OBJECT(
                    "total_crates", COUNT(*),
                    "total_pieces", COALESCE(SUM(pieces), 0),
                    "total_weight", COALESCE(SUM(gross_weight), 0)
                ) INTO result
                FROM crates
                WHERE receiving_plan_id = plan_id;
                
                RETURN result;
            END
        ');

        // Create stored procedure for check-in process
        DB::unprepared('
            CREATE PROCEDURE check_in_crate(
                IN p_crate_id BIGINT,
                IN p_pallet_id VARCHAR(100),
                IN p_user_id BIGINT,
                IN p_device_id VARCHAR(100)
            )
            BEGIN
                DECLARE v_existing_pallet INT DEFAULT 0;
                DECLARE v_crate_status VARCHAR(20);
                DECLARE v_receiving_plan_id BIGINT;
                DECLARE v_new_pallet_id BIGINT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;
                
                START TRANSACTION;
                
                -- Check if crate exists and is in planned status
                SELECT status, receiving_plan_id INTO v_crate_status, v_receiving_plan_id 
                FROM crates 
                WHERE id = p_crate_id;
                
                IF v_crate_status IS NULL THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Crate không tồn tại";
                END IF;
                
                IF v_crate_status != "planned" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Crate đã được check-in hoặc không ở trạng thái planned";
                END IF;
                
                -- Check if pallet_id already exists
                SELECT COUNT(*) INTO v_existing_pallet FROM pallets WHERE pallet_id = p_pallet_id;
                
                IF v_existing_pallet > 0 THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Pallet ID đã tồn tại trong hệ thống";
                END IF;
                
                -- Create new pallet
                INSERT INTO pallets (pallet_id, crate_id, checked_in_by, checked_in_at, status)
                VALUES (p_pallet_id, p_crate_id, p_user_id, NOW(), "in_transit");
                
                SET v_new_pallet_id = LAST_INSERT_ID();
                
                -- Update crate status
                UPDATE crates SET status = "checked_in", updated_at = NOW() WHERE id = p_crate_id;
                
                -- Log the movement
                INSERT INTO inventory_movements (
                    pallet_id, movement_type, movement_date, reference_type, 
                    reference_id, performed_by, device_type, device_id, created_at, updated_at
                ) VALUES (
                    v_new_pallet_id, "check_in", NOW(), "receiving_plan",
                    v_receiving_plan_id, p_user_id, "pda", p_device_id, NOW(), NOW()
                );
                
                COMMIT;
                
                SELECT "success" as result, v_new_pallet_id as pallet_id;
            END
        ');

        // Create stored procedure for assigning pallet to location
        DB::unprepared('
            CREATE PROCEDURE assign_pallet_location(
                IN p_pallet_id BIGINT,
                IN p_location_id BIGINT,
                IN p_user_id BIGINT,
                IN p_device_id VARCHAR(100)
            )
            BEGIN
                DECLARE v_pallet_status VARCHAR(20);
                DECLARE v_location_status VARCHAR(20);
                DECLARE v_old_location_id BIGINT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;
                
                START TRANSACTION;
                
                -- Check pallet status
                SELECT status, location_id INTO v_pallet_status, v_old_location_id
                FROM pallets 
                WHERE id = p_pallet_id;
                
                IF v_pallet_status IS NULL THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Pallet không tồn tại";
                END IF;
                
                -- Check location availability
                SELECT status INTO v_location_status
                FROM warehouse_locations
                WHERE id = p_location_id;
                
                IF v_location_status != "available" THEN
                    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Vị trí không khả dụng";
                END IF;
                
                -- Update old location status if exists
                IF v_old_location_id IS NOT NULL THEN
                    UPDATE warehouse_locations 
                    SET status = "available", updated_at = NOW() 
                    WHERE id = v_old_location_id;
                END IF;
                
                -- Assign pallet to new location
                UPDATE pallets 
                SET location_id = p_location_id, status = "stored", updated_at = NOW()
                WHERE id = p_pallet_id;
                
                -- Update location status
                UPDATE warehouse_locations 
                SET status = "occupied", updated_at = NOW()
                WHERE id = p_location_id;
                
                -- Log the movement
                INSERT INTO inventory_movements (
                    pallet_id, movement_type, from_location_id, to_location_id,
                    movement_date, reference_type, performed_by, device_type, 
                    device_id, created_at, updated_at
                ) VALUES (
                    p_pallet_id, "move", v_old_location_id, p_location_id,
                    NOW(), "manual", p_user_id, "forklift_computer", 
                    p_device_id, NOW(), NOW()
                );
                
                COMMIT;
                
                SELECT "success" as result;
            END
        ');

        // Create trigger to update receiving plan totals
        DB::unprepared('
            CREATE TRIGGER update_receiving_plan_totals_insert
            AFTER INSERT ON crates
            FOR EACH ROW
            BEGIN
                UPDATE receiving_plans 
                SET 
                    total_crates = (SELECT COUNT(*) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    total_pieces = (SELECT COALESCE(SUM(pieces), 0) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    total_weight = (SELECT COALESCE(SUM(gross_weight), 0) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    updated_at = NOW()
                WHERE id = NEW.receiving_plan_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_receiving_plan_totals_update
            AFTER UPDATE ON crates
            FOR EACH ROW
            BEGIN
                UPDATE receiving_plans 
                SET 
                    total_crates = (SELECT COUNT(*) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    total_pieces = (SELECT COALESCE(SUM(pieces), 0) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    total_weight = (SELECT COALESCE(SUM(gross_weight), 0) FROM crates WHERE receiving_plan_id = NEW.receiving_plan_id),
                    updated_at = NOW()
                WHERE id = NEW.receiving_plan_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER update_receiving_plan_totals_delete
            AFTER DELETE ON crates
            FOR EACH ROW
            BEGIN
                UPDATE receiving_plans 
                SET 
                    total_crates = (SELECT COUNT(*) FROM crates WHERE receiving_plan_id = OLD.receiving_plan_id),
                    total_pieces = (SELECT COALESCE(SUM(pieces), 0) FROM crates WHERE receiving_plan_id = OLD.receiving_plan_id),
                    total_weight = (SELECT COALESCE(SUM(gross_weight), 0) FROM crates WHERE receiving_plan_id = OLD.receiving_plan_id),
                    updated_at = NOW()
                WHERE id = OLD.receiving_plan_id;
            END
        ');

        // Create trigger to update shipment totals
        DB::unprepared('
            CREATE TRIGGER update_shipment_totals_insert
            AFTER INSERT ON shipment_items
            FOR EACH ROW
            BEGIN
                UPDATE shipments s
                SET 
                    total_crates = (SELECT COUNT(*) FROM shipment_items si WHERE si.shipment_id = NEW.shipment_id),
                    total_pieces = (SELECT COALESCE(SUM(c.pieces), 0) 
                                   FROM shipment_items si 
                                   JOIN pallets p ON si.pallet_id = p.id 
                                   JOIN crates c ON p.crate_id = c.id 
                                   WHERE si.shipment_id = NEW.shipment_id),
                    total_weight = (SELECT COALESCE(SUM(c.gross_weight), 0) 
                                   FROM shipment_items si 
                                   JOIN pallets p ON si.pallet_id = p.id 
                                   JOIN crates c ON p.crate_id = c.id 
                                   WHERE si.shipment_id = NEW.shipment_id),
                    updated_at = NOW()
                WHERE s.id = NEW.shipment_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip stored procedures and triggers for SQLite
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS update_receiving_plan_totals_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS update_receiving_plan_totals_update');
        DB::unprepared('DROP TRIGGER IF EXISTS update_receiving_plan_totals_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS update_shipment_totals_insert');
        
        // Drop stored procedures
        DB::unprepared('DROP PROCEDURE IF EXISTS check_in_crate');
        DB::unprepared('DROP PROCEDURE IF EXISTS assign_pallet_location');
        
        // Drop function
        DB::unprepared('DROP FUNCTION IF EXISTS calculate_plan_totals');
    }
};
