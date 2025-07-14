# Phân tích chức năng phần mềm quản lý kho

Dựa trên thông tin bạn cung cấp, phần mềm quản lý kho này có ba chức năng chính: **Kế hoạch nhận hàng**, **Check-in (nhập kho)** và **Xuất hàng**. Dưới đây là phân tích chi tiết từng chức năng:

## 1. Kế hoạch nhận hàng (Receiving Plan)

Chức năng này là bước đầu tiên trong quy trình nhập kho, giúp hệ thống biết trước các kiện hàng dự kiến sẽ được nhận.

### Mục đích:
- Tạo và quản lý các kế hoạch nhập hàng từ các nhà cung cấp (Vender)
- Cung cấp thông tin chi tiết về từng kiện hàng dự kiến sẽ đến, giúp chuẩn bị cho quá trình nhập kho

### Thông tin chi tiết bao gồm:
- **Crate ID**: Mã định danh duy nhất cho mỗi kiện hàng
- **Description**: Mô tả chi tiết về nội dung kiện hàng
- **PCS (Pieces)**: Số lượng sản phẩm/đơn vị trong kiện hàng
- **GW (Gross Weight)**: Tổng trọng lượng của kiện hàng (có thể tính bằng kg hoặc tấn)
- **DIM (Dimensions)**: Kích thước của kiện hàng (dài x rộng x cao)
- **Create Datetime**: Thời gian tạo bản ghi kế hoạch nhận hàng
- **Vender**: Thông tin về nhà cung cấp gửi kiện hàng

### Giao diện Web:
- **Hiển thị**: Cho phép người dùng xem danh sách các kế hoạch nhận hàng hiện có và chi tiết của từng kế hoạch
- **Thêm**: Cho phép tạo mới một kế hoạch nhận hàng với tất cả các thông tin trên, có chức năng upload Excel
- **Sửa**: Cho phép chỉnh sửa thông tin của một kế hoạch nhận hàng đã tạo (ví dụ: thay đổi số lượng, mô tả)
- **Xóa**: Cho phép xóa một kế hoạch nhận hàng không còn hiệu lực hoặc bị hủy

## 2. Check-in (Nhập kho và Gán vị trí)

Chức năng này quản lý quá trình thực tế đưa hàng vào kho, bao gồm việc gán Pallet ID và vị trí lưu trữ.

### Mục đích:
- Gán mã Pallet ID cho từng kiện hàng khi chúng được đưa vào kho
- Xác nhận kiện hàng đã được nhận và đưa vào hệ thống kho
- Gán vị trí lưu trữ cụ thể (rack kho) cho từng Pallet ID

### Quy trình và tương tác thiết bị:

#### Sử dụng PDA (Thiết bị di động):
- **Gán Pallet ID**: Nhân viên sử dụng PDA để quét hoặc nhập Crate ID của từng kiện hàng và gán một Pallet ID mới cho nó
- **Xử lý kiện hàng không có Barcode**: Nếu kiện hàng không có Barcode, nhân viên phải chọn thủ công Crate ID tương ứng từ danh sách các kiện hàng trong kế hoạch nhận hàng trên PDA để gán Pallet ID
- **Kiểm tra điều kiện**: Hệ thống sẽ tự động kiểm tra để đảm bảo:
  - Kiện hàng (Crate ID) phải có trong danh sách "Kế hoạch nhận hàng"
  - Kiện hàng đó chưa được "check-in" (chưa được đưa vào kho) trước đó

#### Sử dụng máy tính xe nâng:
- **Gán vị trí**: Sau khi kiện hàng được gán Pallet ID, nhân viên lái xe nâng sử dụng máy tính gắn trên xe để quét Pallet ID và quét mã vị trí rack kho, từ đó gán Pallet ID vào một vị trí lưu trữ cụ thể trong kho

### Giao diện Web:
- **Hiển thị**: Cho phép người dùng xem trạng thái của các kiện hàng đã được check-in, vị trí Pallet ID, và thông tin chi tiết liên quan
- **Thêm/Sửa/Xóa**: Mặc dù quy trình chính được thực hiện qua PDA và máy tính xe nâng, giao diện web vẫn cung cấp khả năng thêm, sửa, xóa thông tin check-in (ví dụ: điều chỉnh vị trí Pallet ID nếu có lỗi, hoặc thêm thủ công trong trường hợp đặc biệt)

## 3. Xuất hàng (Outbound/Shipping)

Chức năng này quản lý quy trình lấy hàng từ kho và chuẩn bị cho việc giao hàng.

### Mục đích:
- Quản lý các yêu cầu xuất hàng
- Hướng dẫn việc lấy hàng từ các vị trí lưu trữ
- Ghi nhận quá trình đưa hàng lên xe và xuất kho
- Tạo biên bản giao hàng

### Quy trình và tương tác thiết bị:

#### Trang hiển thị thông tin xuất hàng (Web):
- **Thông tin hiển thị**: Cung cấp tổng quan về các kiện hàng có sẵn để xuất, bao gồm: Crate ID, Description, PCS, Ngày nhập kho (giúp xác định hàng tồn kho cũ hơn), Vị trí (rack kho), Vender

#### Tải yêu cầu giao hàng lên hệ thống (Web):
- **Người dùng có thể tải lên các file hoặc nhập thủ công thông tin yêu cầu giao hàng**: Chủ yếu là danh sách các Crate ID cần xuất. Hệ thống sẽ xử lý yêu cầu này để tạo ra các lệnh xuất hàng

#### Máy tính xe nâng - Check-out vị trí:
- **Nhân viên lái xe nâng truy cập phần xuất hàng trên máy tính xe nâng**
- **Thực hiện "check-out" các Pallet ID**: Từ vị trí rack kho của chúng ra khu vực thềm chờ xuất hàng (staging area). Điều này cập nhật trạng thái của kiện hàng trong hệ thống

#### PDA - Tạo xe và bắn kiện hàng lên xe:
- **Tạo xe**: Nhân viên sử dụng PDA để tạo một "xe" (đại diện cho một chuyến xe tải hoặc container) trong hệ thống
- **Bắn kiện hàng lên xe**: Từng kiện hàng được đưa lên xe sẽ được quét (hoặc nhập Crate ID) bằng PDA để xác nhận đã được chất lên xe đó
- **Depart xe**: Khi tất cả các kiện hàng đã được chất lên xe và xe sẵn sàng rời đi, nhân viên sẽ thực hiện thao tác "Depart xe" trên PDA, đánh dấu chuyến xe đã hoàn tất việc xuất hàng

### Giao diện Web:
- **Hiển thị**: Cho phép người dùng xem thông tin chi tiết về các chuyến xe xuất hàng, trạng thái của chúng, và các kiện hàng đã được chất lên từng xe
- **Thêm/Sửa/Xóa**: Cung cấp khả năng quản lý thông tin xe xuất hàng (ví dụ: thêm thông tin xe mới, sửa đổi thông tin chuyến đi, hoặc xóa chuyến đi bị hủy)
- **In biên bản giao hàng (POD - Proof of Delivery)**: Hệ thống cho phép in biên bản giao hàng cho từng xe đã "depart", cung cấp tài liệu xác nhận việc giao nhận hàng hóa

---

**Kết luận**: Phân tích này cung cấp cái nhìn sâu hơn về luồng công việc và các tính năng cụ thể của phần mềm quản lý kho bạn đã mô tả.







Dựa trên phân tích README và quy trình warehouse management, đây là các Actions đặc biệt cần có ngoài CRUD cơ bản:

📥 RECEIVING PLAN Actions
1. Bulk Import/Export
Import Excel - Tải lên file Excel chứa nhiều crate
Export Template - Tải xuống template Excel để import
Export Report - Xuất báo cáo kế hoạch nhận hàng
2. Plan Management
Activate Plan - Kích hoạt kế hoạch để bắt đầu nhận hàng
Close Plan - Đóng kế hoạch khi hoàn thành
Duplicate Plan - Sao chép kế hoạch cho lần nhập tiếp theo
📦 CRATES Actions
3. Check-in Process
Check-in Crate - Nhận crate vào kho (từ PDA/Web)
Assign Pallet - Gán crate vào pallet
Print Barcode - In mã vạch cho crate
Bulk Check-in - Nhận nhiều crate cùng lúc
4. Status Management
Mark as Damaged - Đánh dấu hàng bị hỏng
Mark as Lost - Đánh dấu hàng bị mất
Hold/Release - Tạm giữ/Thả hàng
🏭 WAREHOUSE LOCATION Actions
5. Location Management
Assign to Location - Gán pallet vào vị trí cụ thể
Move Location - Di chuyển pallet giữa các vị trí
Block/Unblock Location - Khóa/Mở khóa vị trí kho
Check Location Capacity - Kiểm tra sức chứa vị trí
📤 SHIPPING REQUEST Actions
6. Request Processing
Approve Request - Phê duyệt yêu cầu xuất hàng
Reject Request - Từ chối yêu cầu
Split Request - Tách yêu cầu thành nhiều phần
Generate Pick List - Tạo danh sách lấy hàng
🚛 SHIPMENT Actions
7. Shipment Operations
Create Vehicle - Tạo xe cho chuyến hàng
Load Items - Chất hàng lên xe
Check-out from Location - Lấy hàng từ vị trí kho
Depart Vehicle - Xe rời khỏi kho
Print POD - In biên bản giao hàng
8. Tracking
Track Shipment - Theo dõi trạng thái chuyến hàng
Update Delivery Status - Cập nhật tình trạng giao hàng
Confirm Delivery - Xác nhận đã giao hàng
🚛 VEHICLE Actions
9. Vehicle Operations
Assign Driver - Gán tài xế cho xe
Check Vehicle Status - Kiểm tra tình trạng xe
Schedule Maintenance - Lên lịch bảo trì
Track Vehicle Location - Theo dõi vị trí xe (GPS)
📱 DEVICE Integration Actions
10. PDA/Mobile Actions
Scan Barcode - Quét mã vạch
Manual Entry - Nhập thủ công khi không có barcode
Sync Data - Đồng bộ dữ liệu với server
Offline Mode - Hoạt động offline
📊 REPORTING & ANALYTICS Actions
11. Reports
Inventory Report - Báo cáo tồn kho
Movement Report - Báo cáo di chuyển hàng hóa
Performance Report - Báo cáo hiệu suất
Daily Summary - Tóm tắt hoạt động hàng ngày
12. Dashboard Actions
Refresh Dashboard - Làm mới dashboard
Filter by Date Range - Lọc theo khoảng thời gian
Export Dashboard Data - Xuất dữ liệu dashboard
🔄 WORKFLOW Actions
13. Process Control
Workflow Approval - Phê duyệt quy trình
Emergency Stop - Dừng khẩn cấp quy trình
Resume Process - Tiếp tục quy trình
Rollback - Hoàn tác thao tác
🔔 NOTIFICATION Actions
14. Alerts
Send Alert - Gửi cảnh báo
Mark as Read - Đánh dấu đã đọc
Escalate Issue - Báo cáo vấn đề lên cấp trên
💡 Đề xuất implement priority:
High Priority (Cần có ngay):
✅ Import/Export Excel
✅ Check-in/Check-out Crates
✅ Assign to Location
✅ Create/Depart Vehicle
✅ Print POD
✅ Scan Barcode integration
Medium Priority (Giai đoạn 2):
🔄 Approve/Reject Requests
🔄 Track Shipment
🔄 Reports generation
🔄 Move Location
Low Priority (Tính năng nâng cao):
📱 GPS Vehicle tracking
📱 Offline PDA mode
📊 Advanced analytics
🔔 Real-time notifications
Những actions này sẽ tạo nên một hệ thống warehouse management hoàn chỉnh, đáp ứng đầy đủ quy trình từ nhận hàng đến xuất hàng như mô tả trong README.




