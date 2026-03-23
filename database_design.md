# Thiết kế Database cho Hệ thống Quản lý Kho

## Tổng quan

Hệ thống quản lý kho bao gồm 3 chức năng chính:
1. **Kế hoạch nhận hàng** (Receiving Plan)
2. **Check-in** (Nhập kho và Gán vị trí)
3. **Xuất hàng** (Outbound/Shipping)

## Cấu trúc Database

### 1. Bảng `vendors` - Nhà cung cấp
```sql
CREATE TABLE vendors (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vendor_code VARCHAR(50) UNIQUE NOT NULL,
    vendor_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Bảng `receiving_plans` - Kế hoạch nhận hàng
```sql
CREATE TABLE receiving_plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    plan_code VARCHAR(50) UNIQUE NOT NULL,
    vendor_id BIGINT NOT NULL,
    plan_date DATE NOT NULL,
    total_crates INT DEFAULT 0,
    total_pieces INT DEFAULT 0,
    total_weight DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT,
    
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 3. Bảng `crates` - Kiện hàng
```sql
CREATE TABLE crates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    crate_id VARCHAR(100) UNIQUE NOT NULL,
    receiving_plan_id BIGINT NOT NULL,
    description TEXT,
    pieces INT NOT NULL DEFAULT 0,
    gross_weight DECIMAL(10,2) NOT NULL DEFAULT 0,
    dimensions_length DECIMAL(8,2),
    dimensions_width DECIMAL(8,2),
    dimensions_height DECIMAL(8,2),
    status ENUM('planned', 'checked_in', 'checked_out', 'shipped') DEFAULT 'planned',
    barcode VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (receiving_plan_id) REFERENCES receiving_plans(id) ON DELETE CASCADE
);
```

### 4. Bảng `warehouse_locations` - Vị trí trong kho
```sql
CREATE TABLE warehouse_locations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    location_code VARCHAR(50) UNIQUE NOT NULL,
    zone VARCHAR(50) NOT NULL,
    rack VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    position VARCHAR(50) NOT NULL,
    max_weight DECIMAL(10,2),
    max_volume DECIMAL(10,2),
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5. Bảng `pallets` - Pallet
```sql
CREATE TABLE pallets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pallet_id VARCHAR(100) UNIQUE NOT NULL,
    crate_id BIGINT NOT NULL,
    location_id BIGINT,
    status ENUM('in_transit', 'stored', 'staging', 'shipped') DEFAULT 'in_transit',
    checked_in_at TIMESTAMP,
    checked_in_by BIGINT,
    checked_out_at TIMESTAMP,
    checked_out_by BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (crate_id) REFERENCES crates(id),
    FOREIGN KEY (location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (checked_in_by) REFERENCES users(id),
    FOREIGN KEY (checked_out_by) REFERENCES users(id)
);
```

### 6. Bảng `shipping_requests` - Yêu cầu xuất hàng
```sql
CREATE TABLE shipping_requests (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    request_code VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_contact VARCHAR(255),
    delivery_address TEXT,
    requested_date DATE NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'processing', 'ready', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT,
    
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 7. Bảng `shipping_request_items` - Chi tiết yêu cầu xuất hàng
```sql
CREATE TABLE shipping_request_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    shipping_request_id BIGINT NOT NULL,
    crate_id BIGINT NOT NULL,
    quantity_requested INT NOT NULL DEFAULT 1,
    quantity_shipped INT DEFAULT 0,
    status ENUM('pending', 'allocated', 'picked', 'shipped') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shipping_request_id) REFERENCES shipping_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (crate_id) REFERENCES crates(id)
);
```

### 8. Bảng `vehicles` - Xe vận chuyển
```sql
CREATE TABLE vehicles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    vehicle_code VARCHAR(50) UNIQUE NOT NULL,
    vehicle_type ENUM('truck', 'container', 'van') NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    driver_name VARCHAR(255),
    driver_phone VARCHAR(20),
    capacity_weight DECIMAL(10,2),
    capacity_volume DECIMAL(10,2),
    status ENUM('available', 'loading', 'in_transit', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 9. Bảng `shipments` - Lô hàng xuất
```sql
CREATE TABLE shipments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    shipment_code VARCHAR(50) UNIQUE NOT NULL,
    vehicle_id BIGINT NOT NULL,
    shipping_request_id BIGINT,
    departure_time TIMESTAMP,
    arrival_time TIMESTAMP,
    total_crates INT DEFAULT 0,
    total_pieces INT DEFAULT 0,
    total_weight DECIMAL(10,2) DEFAULT 0,
    status ENUM('loading', 'ready', 'departed', 'delivered', 'returned') DEFAULT 'loading',
    pod_generated BOOLEAN DEFAULT FALSE,
    pod_file_path VARCHAR(500),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT,
    
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (shipping_request_id) REFERENCES shipping_requests(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### 10. Bảng `shipment_items` - Chi tiết lô hàng
```sql
CREATE TABLE shipment_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    shipment_id BIGINT NOT NULL,
    pallet_id BIGINT NOT NULL,
    loaded_at TIMESTAMP,
    loaded_by BIGINT,
    status ENUM('loaded', 'shipped', 'delivered') DEFAULT 'loaded',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
    FOREIGN KEY (pallet_id) REFERENCES pallets(id),
    FOREIGN KEY (loaded_by) REFERENCES users(id)
);
```

### 11. Bảng `inventory_movements` - Lịch sử di chuyển hàng hóa
```sql
CREATE TABLE inventory_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pallet_id BIGINT NOT NULL,
    movement_type ENUM('check_in', 'check_out', 'move', 'adjust') NOT NULL,
    from_location_id BIGINT,
    to_location_id BIGINT,
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reference_type ENUM('receiving_plan', 'shipping_request', 'manual') NOT NULL,
    reference_id BIGINT,
    notes TEXT,
    performed_by BIGINT,
    device_type ENUM('web', 'pda', 'forklift_computer') NOT NULL,
    device_id VARCHAR(100),
    
    FOREIGN KEY (pallet_id) REFERENCES pallets(id),
    FOREIGN KEY (from_location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (to_location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (performed_by) REFERENCES users(id)
);
```

### 12. Bảng `devices` - Thiết bị (PDA, Máy tính xe nâng)
```sql
CREATE TABLE devices (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    device_code VARCHAR(50) UNIQUE NOT NULL,
    device_type ENUM('pda', 'forklift_computer', 'web_terminal') NOT NULL,
    device_name VARCHAR(255) NOT NULL,
    mac_address VARCHAR(17),
    ip_address VARCHAR(15),
    status ENUM('active', 'inactive', 'maintenance') DEFAULT 'active',
    last_sync_at TIMESTAMP,
    assigned_to BIGINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);
```

### 13. Bảng `users` - Người dùng hệ thống
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'warehouse_manager', 'forklift_operator', 'checker', 'viewer') NOT NULL,
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 14. Bảng `audit_logs` - Nhật ký hệ thống
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    record_id BIGINT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Mối quan hệ giữa các bảng

### 1. **Receiving Plan Flow**
- `vendors` → `receiving_plans` → `crates`
- Một nhà cung cấp có nhiều kế hoạch nhận hàng
- Một kế hoạch nhận hàng có nhiều kiện hàng

### 2. **Check-in Flow**
- `crates` → `pallets` → `warehouse_locations`
- Một kiện hàng được gán một pallet
- Một pallet được đặt tại một vị trí trong kho

### 3. **Shipping Flow**
- `shipping_requests` → `shipping_request_items` → `shipments` → `shipment_items`
- Yêu cầu xuất hàng chứa nhiều item
- Nhiều yêu cầu có thể được gộp thành một lô hàng

### 4. **Inventory Tracking**
- `inventory_movements` theo dõi mọi di chuyển của pallet
- `audit_logs` ghi lại mọi thay đổi trong hệ thống

## Indexes để tối ưu hiệu suất

```sql
-- Indexes cho các truy vấn thường xuyên
CREATE INDEX idx_crates_receiving_plan ON crates(receiving_plan_id);
CREATE INDEX idx_crates_status ON crates(status);
CREATE INDEX idx_pallets_crate ON pallets(crate_id);
CREATE INDEX idx_pallets_location ON pallets(location_id);
CREATE INDEX idx_pallets_status ON pallets(status);
CREATE INDEX idx_movements_pallet ON inventory_movements(pallet_id);
CREATE INDEX idx_movements_date ON inventory_movements(movement_date);
CREATE INDEX idx_shipment_items_shipment ON shipment_items(shipment_id);
CREATE INDEX idx_locations_code ON warehouse_locations(location_code);
CREATE INDEX idx_receiving_plans_vendor ON receiving_plans(vendor_id);
CREATE INDEX idx_receiving_plans_status ON receiving_plans(status);
```

## Stored Procedures và Functions quan trọng

### 1. Function tính toán tổng số liệu
```sql
-- Tính tổng crates trong receiving plan
DELIMITER //
CREATE FUNCTION calculate_plan_totals(plan_id BIGINT)
RETURNS JSON
READS SQL DATA
BEGIN
    DECLARE result JSON;
    SELECT JSON_OBJECT(
        'total_crates', COUNT(*),
        'total_pieces', SUM(pieces),
        'total_weight', SUM(gross_weight)
    ) INTO result
    FROM crates
    WHERE receiving_plan_id = plan_id;
    
    RETURN result;
END //
DELIMITER ;
```

### 2. Procedure cho check-in process
```sql
DELIMITER //
CREATE PROCEDURE check_in_crate(
    IN p_crate_id BIGINT,
    IN p_pallet_id VARCHAR(100),
    IN p_user_id BIGINT,
    IN p_device_id VARCHAR(100)
)
BEGIN
    DECLARE v_existing_pallet INT DEFAULT 0;
    DECLARE v_crate_status VARCHAR(20);
    
    -- Kiểm tra crate tồn tại và chưa check-in
    SELECT status INTO v_crate_status FROM crates WHERE id = p_crate_id;
    
    IF v_crate_status != 'planned' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Crate đã được check-in hoặc không tồn tại';
    END IF;
    
    -- Kiểm tra pallet_id không trùng
    SELECT COUNT(*) INTO v_existing_pallet FROM pallets WHERE pallet_id = p_pallet_id;
    
    IF v_existing_pallet > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pallet ID đã tồn tại';
    END IF;
    
    -- Tạo pallet mới
    INSERT INTO pallets (pallet_id, crate_id, checked_in_by, checked_in_at)
    VALUES (p_pallet_id, p_crate_id, p_user_id, NOW());
    
    -- Cập nhật trạng thái crate
    UPDATE crates SET status = 'checked_in' WHERE id = p_crate_id;
    
    -- Ghi log
    INSERT INTO inventory_movements (
        pallet_id, movement_type, movement_date, reference_type, 
        reference_id, performed_by, device_type, device_id
    ) VALUES (
        LAST_INSERT_ID(), 'check_in', NOW(), 'receiving_plan',
        (SELECT receiving_plan_id FROM crates WHERE id = p_crate_id),
        p_user_id, 'pda', p_device_id
    );
    
END //
DELIMITER ;
```

---

**Ghi chú**: Thiết kế database này đã được tối ưu để hỗ trợ đầy đủ các chức năng được mô tả trong README.md và có thể mở rộng dễ dàng cho các tính năng tương lai.
