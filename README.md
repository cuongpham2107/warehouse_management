Phân tích chức năng phần mềm quản lý kho
Dựa trên thông tin bạn cung cấp, phần mềm quản lý kho này có ba chức năng chính: Kế hoạch nhận hàng, Check-in (nhập kho) và Xuất hàng. Dưới đây là phân tích chi tiết từng chức năng:
1. Kế hoạch nhận hàng (Receiving Plan)
Chức năng này là bước đầu tiên trong quy trình nhập kho, giúp hệ thống biết trước các kiện hàng dự kiến sẽ được nhận.
•	Mục đích:
o	Tạo và quản lý các kế hoạch nhập hàng từ các nhà cung cấp (Vender).
o	Cung cấp thông tin chi tiết về từng kiện hàng dự kiến sẽ đến, giúp chuẩn bị cho quá trình nhập kho.
•	Thông tin chi tiết bao gồm:
o	Crate ID: Mã định danh duy nhất cho mỗi kiện hàng.
o	Description: Mô tả chi tiết về nội dung kiện hàng.
o	PCS (Pieces): Số lượng sản phẩm/đơn vị trong kiện hàng.
o	GW (Gross Weight): Tổng trọng lượng của kiện hàng (có thể tính bằng kg hoặc tấn).
o	DIM (Dimensions): Kích thước của kiện hàng (dài x rộng x cao).
o	Create Datetime: Thời gian tạo bản ghi kế hoạch nhận hàng.
o	Vender: Thông tin về nhà cung cấp gửi kiện hàng.
•	Giao diện Web:
o	Hiển thị: Cho phép người dùng xem danh sách các kế hoạch nhận hàng hiện có và chi tiết của từng kế hoạch.
o	Thêm: Cho phép tạo mới một kế hoạch nhận hàng với tất cả các thông tin trên, có chức năng up excel
o	Sửa: Cho phép chỉnh sửa thông tin của một kế hoạch nhận hàng đã tạo (ví dụ: thay đổi số lượng, mô tả).
o	Xóa: Cho phép xóa một kế hoạch nhận hàng không còn hiệu lực hoặc bị hủy.
2. Check-in (Nhập kho và Gán vị trí)
Chức năng này quản lý quá trình thực tế đưa hàng vào kho, bao gồm việc gán Pallet ID và vị trí lưu trữ.
•	Mục đích:
o	Gán mã Pallet ID cho từng kiện hàng khi chúng được đưa vào kho.
o	Xác nhận kiện hàng đã được nhận và đưa vào hệ thống kho.
o	Gán vị trí lưu trữ cụ thể (rack kho) cho từng Pallet ID.
•	Quy trình và tương tác thiết bị:
o	Sử dụng PDA (Thiết bị di động):
	Gán Pallet ID: Nhân viên sử dụng PDA để quét hoặc nhập Crate ID của từng kiện hàng và gán một Pallet ID mới cho nó.
	Xử lý kiện hàng không có Barcode: Nếu kiện hàng không có Barcode, nhân viên phải chọn thủ công Crate ID tương ứng từ danh sách các kiện hàng trong kế hoạch nhận hàng trên PDA để gán Pallet ID.
	Kiểm tra điều kiện: Hệ thống sẽ tự động kiểm tra để đảm bảo:
	Kiện hàng (Crate ID) phải có trong danh sách "Kế hoạch nhận hàng".
	Kiện hàng đó chưa được "check-in" (chưa được đưa vào kho) trước đó.
o	Sử dụng máy tính xe nâng:
	Gán vị trí: Sau khi kiện hàng được gán Pallet ID, nhân viên lái xe nâng sử dụng máy tính gắn trên xe để quét Pallet ID và quét mã vị trí rack kho, từ đó gán Pallet ID vào một vị trí lưu trữ cụ thể trong kho.
•	Giao diện Web:
o	Hiển thị: Cho phép người dùng xem trạng thái của các kiện hàng đã được check-in, vị trí Pallet ID, và thông tin chi tiết liên quan.
o	Thêm/Sửa/Xóa: Mặc dù quy trình chính được thực hiện qua PDA và máy tính xe nâng, giao diện web vẫn cung cấp khả năng thêm, sửa, xóa thông tin check-in (ví dụ: điều chỉnh vị trí Pallet ID nếu có lỗi, hoặc thêm thủ công trong trường hợp đặc biệt).
3. Xuất hàng (Outbound/Shipping)
Chức năng này quản lý quy trình lấy hàng từ kho và chuẩn bị cho việc giao hàng.
•	Mục đích:
o	Quản lý các yêu cầu xuất hàng.
o	Hướng dẫn việc lấy hàng từ các vị trí lưu trữ.
o	Ghi nhận quá trình đưa hàng lên xe và xuất kho.
o	Tạo biên bản giao hàng.
•	Quy trình và tương tác thiết bị:
o	Trang hiển thị thông tin xuất hàng (Web):
	Thông tin hiển thị: Cung cấp tổng quan về các kiện hàng có sẵn để xuất, bao gồm: Crate ID, Description, PCS, Ngày nhập kho (giúp xác định hàng tồn kho cũ hơn), Vị trí (rack kho), Vender.
o	Tải yêu cầu giao hàng lên hệ thống (Web):
	Người dùng có thể tải lên các file hoặc nhập thủ công thông tin yêu cầu giao hàng, chủ yếu là danh sách các Crate ID cần xuất. Hệ thống sẽ xử lý yêu cầu này để tạo ra các lệnh xuất hàng.
o	Máy tính xe nâng - Check-out vị trí:
	Nhân viên lái xe nâng truy cập phần xuất hàng trên máy tính xe nâng.
	Thực hiện "check-out" các Pallet ID từ vị trí rack kho của chúng ra khu vực thềm chờ xuất hàng (staging area). Điều này cập nhật trạng thái của kiện hàng trong hệ thống.
o	PDA - Tạo xe và bắn kiện hàng lên xe:
	Tạo xe: Nhân viên sử dụng PDA để tạo một "xe" (đại diện cho một chuyến xe tải hoặc container) trong hệ thống.
	Bắn kiện hàng lên xe: Từng kiện hàng được đưa lên xe sẽ được quét (hoặc nhập Crate ID) bằng PDA để xác nhận đã được chất lên xe đó.
	Depart xe: Khi tất cả các kiện hàng đã được chất lên xe và xe sẵn sàng rời đi, nhân viên sẽ thực hiện thao tác "Depart xe" trên PDA, đánh dấu chuyến xe đã hoàn tất việc xuất hàng.
•	Giao diện Web:
o	Hiển thị: Cho phép người dùng xem thông tin chi tiết về các chuyến xe xuất hàng, trạng thái của chúng, và các kiện hàng đã được chất lên từng xe.
o	Thêm/Sửa/Xóa: Cung cấp khả năng quản lý thông tin xe xuất hàng (ví dụ: thêm thông tin xe mới, sửa đổi thông tin chuyến đi, hoặc xóa chuyến đi bị hủy).
o	In biên bản giao hàng (POD - Proof of Delivery): Hệ thống cho phép in biên bản giao hàng cho từng xe đã "depart", cung cấp tài liệu xác nhận việc giao nhận hàng hóa.
Phân tích này cung cấp cái nhìn sâu hơn về luồng công việc và các tính năng cụ thể của phần mềm quản lý kho bạn đã mô tả.

