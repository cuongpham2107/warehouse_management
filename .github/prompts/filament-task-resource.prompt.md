## Filament v4 – Task Resource (CRUD)

Bạn là trợ lý phát triển Laravel + Filament v4. Giúp tôi tạo một **Filament Resource** cho model `Task` với các yêu cầu:

- Bảng `tasks` chứa các cột: `title` (string), `description` (text), `status` (enum: pending/in_progress/completed), `due_date` (date), `assigned_to` (relation user).
- Tạo **Resource** dùng schema mới (separate files), gồm:
  - `TaskForm.php`
  - `TaskTable.php`
- Form gồm các field: text + textarea + select (cho status) + date picker + relation manager cho `assigned_to`.
- Table gồm các column tương ứng: title, status (colored badge), due_date, assigned user.
- Table hỗ trợ search trên title + filter theo status.
- Form có validation: title max 255, due_date after today.
- Sau khi tạo record thành công, redirect về index và show notification "Task created successfully".

> Xuất ra 4 file đầy đủ code: Resource, Form schema, Table schema, Migration nếu cần.

---

🛠 **Nếu có chỗ mô tả Filament hoặc cách dùng không rõ**, hãy sử dụng **MCP search web** (Model Context Protocol) để tự động tra cứu trong document chính thức của Filament v4, đảm bảo bạn lấy được thông tin chuẩn xác từ tài liệu.  
Ví dụ: “MCP tool: describe_filament_resource TaskResource” để lấy các trường, filter, relationship có sẵn.
