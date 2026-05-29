<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. CLUBS (Các chi nhánh câu lạc bộ)
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->timestamps();
        });

        // 2. USERS (Kế thừa bảng users mặc định của Laravel)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['manager', 'admin', 'user'])->default('user');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. TABLES (Danh sách bàn bida)
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_per_hour', 12, 2);
            $table->timestamps();
        });

        // 4. SERVICES (Thức uống, đồ ăn, dịch vụ dịch kèm)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->string('category')->default('thức uống'); // thức uống, đồ ăn, khác
            $table->timestamps();
        });

        // 5. ACTIVE SESSIONS (Các phiên chơi đang hoạt động)
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('start_time')->useCurrent();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // 6. SESSION SERVICES (Mối liên kết giữa Session đang chơi và Dịch vụ gọi kèm)
        Schema::create('session_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });

        // 7. INVOICES (Lịch sử hóa đơn đã thanh toán)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->foreignId('session_id')->nullable();
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->useCurrent();
            $table->integer('duration_minutes');
            $table->decimal('table_fee', 12, 2);
            $table->decimal('services_fee', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        // 8. INVOICE ITEMS (Lưu vết dịch vụ tại thời điểm in hóa đơn)
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 9. DEBTS (Hồ sơ khách nợ)
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('customer_name');
            $table->decimal('total_debt', 12, 2)->default(0);
            $table->timestamps();
        });

        // 10. DEBT TRANSACTIONS (Nhật ký ghi nợ và thanh toán trả nợ)
        Schema::create('debt_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Dương (+) là nợ thêm, Âm (-) là trả nợ
            $table->string('note')->nullable();
            $table->timestamps();
        });

        // 11. NOTES (Sổ ghi chú công việc nội bộ chi nhánh)
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 12. ACTIVITY LOGS (Ghi nhật ký hệ thống)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->nullable()->constrained('clubs')->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('debt_transactions');
        Schema::dropIfExists('debts');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('session_services');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('services');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('users');
        Schema::dropIfExists('clubs');
    }
};
