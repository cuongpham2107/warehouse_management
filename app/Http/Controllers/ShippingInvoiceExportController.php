<?php

namespace App\Http\Controllers;

use App\Models\ShippingRequest;
use App\Exports\ExportInvoiceShipping;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShippingInvoiceExportController extends Controller
{

    public function export($id)
    {
        // Check ZIP extension
        if (!extension_loaded('zip')) {
            throw new \Exception('PHP ZIP extension is not installed');
        }

        $shippingRequest = ShippingRequest::with('items')->findOrFail($id);
        $items = $shippingRequest->items;

        // DEBUG: Kiểm tra dữ liệu
        Log::info('Shipping Request ID: ' . $id);
        Log::info('Items count: ' . $items->count());
        Log::info('Items data: ' . json_encode($items->toArray()));

        // Kiểm tra nếu không có items
        if ($items->isEmpty()) {
            throw new \Exception('Không có items nào trong shipping request này');
        }

        // Chia item thành các chunk 20 bản ghi
        $chunks = collect($items)->chunk(20)->all();

        // DEBUG: Kiểm tra chunks
        Log::info('Chunks count: ' . count($chunks));
        foreach ($chunks as $index => $chunk) {
            Log::info("Chunk {$index}: " . $chunk->count() . ' items');
        }

        $filePaths = [];
        $zipFileName = 'shipping_invoices_' . now()->format('Ymd_His') . '.zip';
        $zipPath = public_path($zipFileName);

        // Sử dụng thư mục public/temp_excels
        $tempDir = public_path('temp_excels');

        // Tạo thư mục nếu chưa có và kiểm tra quyền
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw new \Exception("Không thể tạo thư mục: {$tempDir}");
            }
        }

        // Kiểm tra quyền ghi
        if (!is_writable($tempDir)) {
            throw new \Exception("Thư mục không có quyền ghi: {$tempDir}");
        }

        if (!is_writable(public_path())) {
            throw new \Exception("Thư mục public không có quyền ghi");
        }

        try {
            foreach ($chunks as $index => $chunk) {
                Log::info("Processing chunk {$index} with " . $chunk->count() . " items");

                $fileName = "shipping_invoice_part_" . ($index + 1) . ".xlsx";
                $fullPath = $tempDir . DIRECTORY_SEPARATOR . $fileName;

                try {
                    // DEBUG: Kiểm tra ExportInvoiceShipping class
                    Log::info("Creating ExportInvoiceShipping instance");
                    $exporter = new ExportInvoiceShipping($shippingRequest, $chunk);

                    // Tạo nội dung Excel
                    Log::info("Generating Excel content");
                    $content = Excel::raw($exporter, \Maatwebsite\Excel\Excel::XLSX);

                    // DEBUG: Kiểm tra nội dung
                    Log::info("Excel content size: " . strlen($content) . " bytes");

                    if (empty($content)) {
                        throw new \Exception("Excel content is empty for chunk {$index}");
                    }

                    // Ghi file và kiểm tra
                    Log::info("Writing file to: {$fullPath}");
                    $success = file_put_contents($fullPath, $content);

                    if ($success === false) {
                        throw new \Exception("file_put_contents failed for: {$fullPath}");
                    }

                    if (!file_exists($fullPath)) {
                        throw new \Exception("File was not created: {$fullPath}");
                    }

                    $fileSize = filesize($fullPath);
                    Log::info("File created successfully: {$fullPath}, Size: {$fileSize} bytes");

                    if ($fileSize === 0) {
                        throw new \Exception("File is empty: {$fullPath}");
                    }

                    $filePaths[] = $fullPath;
                    Log::info("Added to filePaths. Total files: " . count($filePaths));
                } catch (\Exception $e) {
                    Log::error("Error processing chunk {$index}: " . $e->getMessage());
                    throw new \Exception("Lỗi tạo Excel chunk {$index}: " . $e->getMessage());
                }
            }

            // Kiểm tra có file nào được tạo không
            Log::info("Total files created: " . count($filePaths));
            if (empty($filePaths)) {
                throw new \Exception("Không có file Excel nào được tạo. Chunks count: " . count($chunks));
            }

            // Tạo file zip với error handling chi tiết
            $zip = new ZipArchive();
            $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($result !== TRUE) {
                $errorMessages = [
                    ZipArchive::ER_OK => 'No error',
                    ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
                    ZipArchive::ER_RENAME => 'Renaming temporary file failed',
                    ZipArchive::ER_CLOSE => 'Closing zip archive failed',
                    ZipArchive::ER_SEEK => 'Seek error',
                    ZipArchive::ER_READ => 'Read error',
                    ZipArchive::ER_WRITE => 'Write error',
                    ZipArchive::ER_CRC => 'CRC error',
                    ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
                    ZipArchive::ER_NOENT => 'No such file',
                    ZipArchive::ER_EXISTS => 'File already exists',
                    ZipArchive::ER_OPEN => 'Can\'t open file',
                    ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
                    ZipArchive::ER_ZLIB => 'Zlib error',
                    ZipArchive::ER_MEMORY => 'Memory allocation failure',
                    ZipArchive::ER_CHANGED => 'Entry has been changed',
                    ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
                    ZipArchive::ER_EOF => 'Premature EOF',
                    ZipArchive::ER_INVAL => 'Invalid argument',
                    ZipArchive::ER_NOZIP => 'Not a zip archive',
                    ZipArchive::ER_INTERNAL => 'Internal error',
                    ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                    ZipArchive::ER_REMOVE => 'Can\'t remove file',
                    ZipArchive::ER_DELETED => 'Entry has been deleted',
                ];

                $errorMsg = isset($errorMessages[$result]) ? $errorMessages[$result] : 'Unknown error';
                throw new \Exception("Không thể tạo file ZIP: {$errorMsg} (Code: {$result})");
            }

            foreach ($filePaths as $path) {
                if (file_exists($path)) {
                    $addResult = $zip->addFile($path, basename($path));
                    if (!$addResult) {
                        throw new \Exception("Không thể thêm file vào ZIP: " . basename($path));
                    }
                }
            }

            $closeResult = $zip->close();
            if (!$closeResult) {
                throw new \Exception("Không thể đóng file ZIP");
            }

            // Kiểm tra file ZIP đã được tạo thành công
            if (!file_exists($zipPath) || filesize($zipPath) === 0) {
                throw new \Exception("File ZIP không được tạo hoặc rỗng: {$zipPath}");
            }

            // Xóa các file Excel tạm
            foreach ($filePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            // Log thành công để debug
            Log::info("ZIP file created successfully: {$zipPath}, Size: " . filesize($zipPath) . " bytes");

            // Trả file zip để download
            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Dọn dẹp nếu có lỗi
            foreach ($filePaths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            // Xóa ZIP file nếu đã tạo
            if (isset($zipPath) && file_exists($zipPath)) {
                unlink($zipPath);
            }

            Log::error("Export error: " . $e->getMessage());
            throw $e;
        }
    }
}
