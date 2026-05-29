<x-app-layout>
    <div class="py-6 sm:py-8" x-data="{ 
        activeTab: '{{ request('tab', 'dashboard') }}',
        showEditTableModal: false,
        editTableData: { id: '', name: '', price_per_hour: 0, is_cafe: false },
        showEditServiceModal: false,
        editServiceData: { id: '', name: '', price: 0, category: 'thức uống' },
        showHistoryInvoiceModal: false,
        selectedInvoice: null,
        invoicesList: {{ \Illuminate\Support\Js::from($invoices->concat($todayInvoices)->unique('id')->values()) }},
        openInvoiceModal(id) {
            this.selectedInvoice = this.invoicesList.find(inv => inv.id === id);
            this.showHistoryInvoiceModal = true;
        },
        formatVND(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace('₫', 'đ');
        },
        formatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        }
    }">
        
        <!-- Shared Sub Navigation Tabs -->
        @include('layouts.admin-sub-header')

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
            
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 shadow-md flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tab Contents -->
            <div class="space-y-6">
                
                <!-- 0. DASHBOARD OVERVIEW TAB -->
                <div x-show="activeTab === 'dashboard'" class="space-y-6" x-cloak>
                    <!-- Metrics Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Card 1: Bàn đang chơi -->
                        <div class="bg-emerald-500 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between h-40">
                            <div class="text-xs font-bold uppercase tracking-wider opacity-90">Bàn đang chơi</div>
                            <div class="text-5xl font-black mt-2">{{ $activeTablesCount }}</div>
                            <div class="text-[10px] font-medium opacity-80 mt-auto">Chi nhánh: {{ Auth::user()->club->name ?? 'Mặc định' }}</div>
                        </div>

                        <!-- Card 2: Bàn trống -->
                        <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between h-40">
                            <div class="text-xs font-bold uppercase tracking-wider opacity-90">Bàn trống</div>
                            <div class="text-5xl font-black mt-2">{{ $emptyTablesCount }}</div>
                            <div class="text-[10px] font-medium opacity-80 mt-auto">Sẵn sàng phục vụ</div>
                        </div>

                        <!-- Card 3: Hóa đơn hôm nay -->
                        <div class="bg-amber-500 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between h-40">
                            <div class="text-xs font-bold uppercase tracking-wider opacity-90">Hóa đơn hôm nay</div>
                            <div class="text-5xl font-black mt-2">{{ $invoicesTodayCount }}</div>
                            <div class="text-[10px] font-medium opacity-80 mt-auto">Đã thanh toán</div>
                        </div>

                        <!-- Card 4: Doanh thu hôm nay -->
                        <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg flex flex-col justify-between h-40">
                            <div class="text-xs font-bold uppercase tracking-wider opacity-90">Doanh thu hôm nay</div>
                            <div class="text-3xl font-black mt-2 truncate">{{ number_format($revenueToday, 0, ',', '.') }}đ</div>
                            <div class="text-[10px] font-medium opacity-80 mt-auto">Cập nhật thời gian thực</div>
                        </div>
                    </div>

                    <!-- Bàn Đang Sử Dụng Section -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/20">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Bàn Đang Sử Dụng</h3>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Đang hoạt động: {{ $activeTablesCount }} bàn</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        <th class="py-4 px-6">Bàn</th>
                                        <th class="py-4 px-6">Giờ bắt đầu</th>
                                        <th class="py-4 px-6">Thời gian chơi</th>
                                        <th class="py-4 px-6">Nhân viên</th>
                                        <th class="py-4 px-6 text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                    @forelse($activeSessions as $session)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-4 px-6 font-bold text-slate-800">{{ $session->table->name }}</td>
                                            <td class="py-4 px-6 text-slate-500 font-semibold">{{ $session->start_time->format('d/m/Y H:i:s') }}</td>
                                            <td class="py-4 px-6 font-bold text-slate-700">
                                                @php
                                                    $diffMin = $session->start_time->diffInMinutes(now());
                                                    $h = floor($diffMin / 60);
                                                    $m = $diffMin % 60;
                                                    echo $h . 'h ' . $m . 'm';
                                                @endphp
                                            </td>
                                            <td class="py-4 px-6 text-slate-500">{{ $session->employee->name ?? $session->employee->username ?? 'N/A' }}</td>
                                            <td class="py-4 px-6 text-center">
                                                <a href="{{ route('play-tables.index') }}" 
                                                   class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl text-xs shadow-md shadow-indigo-600/10 transition active:scale-95">
                                                    Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-12 text-center text-slate-400 italic">Không có bàn nào đang sử dụng</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Hóa Đơn Hôm Nay Section -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/20">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Hóa Đơn Hôm Nay</h3>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tổng cộng: {{ $todayInvoices->count() }} hóa đơn</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        <th class="py-4 px-6">Bàn chơi</th>
                                        <th class="py-4 px-6">Nhân viên</th>
                                        <th class="py-4 px-6">Thời gian chơi</th>
                                        <th class="py-4 px-6 text-right">Tiền bàn</th>
                                        <th class="py-4 px-6 text-right">Tiền dịch vụ</th>
                                        <th class="py-4 px-6 text-right text-slate-650">Tổng cộng</th>
                                        <th class="py-4 px-6 text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                    @forelse($todayInvoices as $inv)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-4 px-6 font-bold text-slate-800">{{ $inv->table->name }}</td>
                                            <td class="py-4 px-6">{{ $inv->employee->name ?? 'N/A' }}</td>
                                            <td class="py-4 px-6 text-xs text-slate-500 font-semibold">
                                                {{ $inv->start_time->format('H:i') }} - {{ $inv->end_time->format('H:i') }} ({{ $inv->duration_minutes }} phút)
                                            </td>
                                            <td class="py-4 px-6 text-right font-semibold text-slate-500">{{ number_format($inv->table_fee) }}đ</td>
                                            <td class="py-4 px-6 text-right font-semibold text-slate-500">{{ number_format($inv->services_fee) }}đ</td>
                                            <td class="py-4 px-6 text-right font-extrabold theme-primary-text">{{ number_format($inv->total) }}đ</td>
                                            <td class="py-4 px-6 text-center">
                                                <button type="button" @click="openInvoiceModal({{ $inv->id }})" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-650 hover:bg-indigo-100 transition active:scale-95">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Xem hóa đơn
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-12 text-center text-slate-400 italic">Chưa có hóa đơn nào trong hôm nay</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 1. INVOICES TAB -->
                <div x-show="activeTab === 'invoices'" class="space-y-6" x-cloak>
                    <!-- Filter bar -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <input type="hidden" name="tab" value="invoices">
                            
                            <div class="space-y-1.5">
                                <label for="table_filter" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Lọc Theo Bàn</label>
                                <select name="table_id" id="table_filter" 
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                                    <option value="">-- Tất cả bàn --</option>
                                    @foreach($tables as $t)
                                        <option value="{{ $t->id }}" {{ $selectedTableId == $t->id ? 'selected' : '' }}>
                                            {{ $t->name }} ({{ $t->price_per_hour == 0 ? 'Café' : number_format($t->price_per_hour).'đ/h' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="date_filter" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Chọn ngày xuất hóa đơn</label>
                                <input type="date" name="date" id="date_filter" value="{{ $selectedDate ?? date('Y-m-d') }}"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95 flex items-center justify-center gap-1.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    Tìm Kiếm
                                </button>
                                <a href="{{ route('admin.dashboard', ['tab' => 'invoices']) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold px-4 py-3 rounded-xl text-sm transition active:scale-95 flex items-center justify-center">
                                    Xóa lọc
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Invoices list -->
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                            <h3 class="text-lg font-black text-slate-800">Lịch Sử Hóa Đơn</h3>
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tối đa 10 ngày gần nhất</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        <th class="py-4 px-6">Bàn chơi</th>
                                        <th class="py-4 px-6">Nhân viên</th>
                                        <th class="py-4 px-6">Thời gian chơi</th>
                                        <th class="py-4 px-6 text-right">Tiền bàn</th>
                                        <th class="py-4 px-6 text-right">Tiền dịch vụ</th>
                                        <th class="py-4 px-6 text-right text-slate-650">Tổng cộng</th>
                                        <th class="py-4 px-6 text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                    @forelse($invoices as $inv)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-4 px-6 font-bold text-slate-800">{{ $inv->table->name }}</td>
                                            <td class="py-4 px-6">{{ $inv->employee->name ?? 'N/A' }}</td>
                                            <td class="py-4 px-6 space-y-0.5">
                                                <div class="text-xs font-bold text-slate-500">
                                                    {{ $inv->start_time->format('H:i') }} - {{ $inv->end_time->format('H:i') }} ({{ $inv->created_at->format('d/m/Y') }})
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-semibold uppercase">
                                                    Tổng cộng: {{ $inv->duration_minutes }} phút
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-right font-semibold text-slate-500">{{ number_format($inv->table_fee) }}đ</td>
                                            <td class="py-4 px-6 text-right font-semibold text-slate-500">{{ number_format($inv->services_fee) }}đ</td>
                                            <td class="py-4 px-6 text-right font-extrabold theme-primary-text">{{ number_format($inv->total) }}đ</td>
                                            <td class="py-4 px-6 text-center">
                                                <button type="button" @click="openInvoiceModal({{ $inv->id }})" 
                                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition active:scale-95">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    Xem hóa đơn
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-12 text-center text-slate-400">
                                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path></svg>
                                                <span class="font-bold">Không tìm thấy hóa đơn nào trong ngày chọn</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. STAFF TAB -->
                <div x-show="activeTab === 'staff'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
                    <!-- Form card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm h-fit">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Thêm Nhân Viên</h3>
                        
                        <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="staff_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Họ và tên nhân viên</label>
                                <input type="text" name="name" id="staff_name" required placeholder="Ví dụ: Nguyễn Văn A"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label for="staff_username" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên đăng nhập (Username)</label>
                                <input type="text" name="username" id="staff_username" required placeholder="Ví dụ: nhanvien_1"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label for="staff_password" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Mật khẩu ban đầu</label>
                                <input type="password" name="password" id="staff_password" required placeholder="Tối thiểu 6 ký tự"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <button type="submit" class="w-full theme-primary-bg theme-primary-hover-bg text-white font-bold py-3.5 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Tạo tài khoản nhân viên
                            </button>
                        </form>
                    </div>

                    <!-- List card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Danh Sách Nhân Viên</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($staff as $st)
                                <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-650 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="text-sm font-extrabold text-slate-800 leading-tight">{{ $st->name }}</h4>
                                        <p class="text-xs text-slate-400 font-medium">Username: <span class="font-bold text-slate-600">{{ $st->username }}</span></p>
                                    </div>
                                    <div class="ml-auto flex items-center gap-2">
                                        <button @click="$dispatch('open-edit-employee', { id: '{{ $st->id }}', name: '{{ addslashes($st->name) }}', username: '{{ addslashes($st->username) }}' })"
                                                class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition active:scale-95" title="Sửa nhân viên">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button @click="$dispatch('open-delete-employee', { id: '{{ $st->id }}', name: '{{ addslashes($st->name) }}' })"
                                                class="text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition active:scale-95" title="Xóa nhân viên">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-8 text-center text-slate-400">
                                    <span class="font-bold">Chưa tạo tài khoản nhân viên nào</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 3. TABLES TAB -->
                <div x-show="activeTab === 'tables'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
                    <!-- Form card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm h-fit" x-data="{ isCafe: false }">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Thêm Bàn Chơi Mới</h3>
                        
                        <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="table_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên bàn chơi</label>
                                <input type="text" name="name" id="table_name" required placeholder="Ví dụ: Bàn 01, Bàn 02"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="flex items-center gap-2 py-2">
                                <input type="checkbox" name="is_cafe" id="is_cafe" x-model="isCafe" value="1"
                                       class="w-5 h-5 rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 cursor-pointer accent-indigo-600">
                                <label for="is_cafe" class="text-xs font-extrabold text-slate-700 select-none cursor-pointer">Đây là bàn Cafe (Miễn phí tiền giờ)</label>
                            </div>

                            <div class="space-y-1.5" x-show="!isCafe">
                                <label for="price_per_hour" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Đơn giá thuê (đồng/giờ)</label>
                                <input type="number" name="price_per_hour" id="price_per_hour" placeholder="Ví dụ: 50000" min="0" :required="!isCafe"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <button type="submit" class="w-full theme-primary-bg theme-primary-hover-bg text-white font-bold py-3.5 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Tạo bàn chơi mới
                            </button>
                        </form>
                    </div>

                    <!-- List card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Danh Sách Bàn Chơi</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($tables as $tb)
                                <div class="bg-white border border-slate-200 rounded-2xl p-4 flex flex-col justify-between shadow-sm hover:shadow-md transition duration-200 relative overflow-hidden">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wide truncate max-w-[120px]">{{ $tb->name }}</h4>
                                            @if($tb->price_per_hour == 0)
                                                <span class="bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase px-2 py-0.5 rounded-full border border-emerald-100">Cafe</span>
                                            @else
                                                <span class="bg-indigo-50 text-indigo-700 text-[9px] font-black uppercase px-2 py-0.5 rounded-full border border-indigo-100">Billiards</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Price per hour -->
                                        <div class="flex items-center gap-1.5 text-xs text-slate-650 font-bold pt-1.5 border-t border-slate-100/70">
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Giá:</span>
                                            <span class="font-extrabold text-slate-800">{{ $tb->price_per_hour == 0 ? 'Miễn phí' : number_format($tb->price_per_hour, 0, ',', '.') . 'đ/giờ' }}</span>
                                        </div>

                                        <!-- Created Date -->
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-bold mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span>Tạo lúc:</span>
                                            <span class="font-extrabold">{{ $tb->created_at->format('d/m/Y H:i:s') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-1.5 pt-2.5 border-t border-slate-100/70 mt-3">
                                        <button @click="editTableData = { id: '{{ $tb->id }}', name: '{{ $tb->name }}', price_per_hour: '{{ $tb->price_per_hour }}', is_cafe: {{ $tb->price_per_hour == 0 ? 'true' : 'false' }} }; showEditTableModal = true"
                                                class="inline-flex items-center gap-1 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 text-indigo-700 px-2.5 py-1.5 rounded-xl font-bold text-[10px] transition active:scale-95">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path></svg>
                                            Sửa
                                        </button>
                                        <form action="{{ route('admin.tables.destroy') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $tb->id }}">
                                            <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa bàn chơi này?')"
                                                    class="inline-flex items-center gap-1 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-700 px-2.5 py-1.5 rounded-xl font-bold text-[10px] transition active:scale-95">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-8 text-center text-slate-400">
                                    <span class="font-bold">Chưa tạo bàn chơi nào</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 4. SERVICES TAB -->
                <div x-show="activeTab === 'services'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
                    <!-- Form card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm h-fit">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Thêm Dịch Vụ Mới</h3>
                        
                        <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="service_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên món / dịch vụ</label>
                                <input type="text" name="name" id="service_name" required placeholder="Ví dụ: Bò húc lon, Mì tôm trứng"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label for="service_category" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Phân Loại Tab</label>
                                <select name="category" id="service_category" required
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                                    <option value="thức uống">Thức uống</option>
                                    <option value="đồ ăn">Đồ ăn</option>
                                    <option value="khác">Khác (Ví dụ: thuốc lá, khăn lạnh...)</option>
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label for="service_price" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Đơn giá (đồng)</label>
                                <input type="number" name="price" id="service_price" required placeholder="Ví dụ: 15000" min="0"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <button type="submit" class="w-full theme-primary-bg theme-primary-hover-bg text-white font-bold py-3.5 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Thêm vào menu
                            </button>
                        </form>
                    </div>

                    <!-- List card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm lg:col-span-2 space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base font-black text-slate-800 uppercase tracking-wider">Danh Sách Thực Đơn</h3>
                        </div>

                        <!-- Categories grids -->
                        @php
                            $groupedServices = $services->groupBy('category');
                            $categories = [
                                'thức uống' => '🥤 Thức Uống',
                                'đồ ăn' => '🍳 Đồ Ăn',
                                'khác' => '⚡ Khác'
                            ];
                        @endphp

                        <div class="space-y-6">
                            @foreach($categories as $catKey => $catLabel)
                                @php
                                    $catList = $groupedServices->get($catKey, collect());
                                 @endphp
                                <div class="space-y-3">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-1 flex justify-between items-center">
                                        <span>{{ $catLabel }}</span>
                                        <span>({{ $catList->count() }} món)</span>
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @forelse($catList as $srv)
                                            <div class="bg-white border border-slate-200 rounded-xl p-3 flex justify-between items-center shadow-sm hover:shadow-md transition duration-200">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                                                        @if($srv->category === 'thức uống')
                                                            🥤
                                                        @elseif($srv->category === 'đồ ăn')
                                                            🍳
                                                        @else
                                                            ⚡
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="text-xs font-bold text-slate-800 block truncate max-w-[110px] sm:max-w-xs">{{ $srv->name }}</span>
                                                        <span class="text-[10px] font-black text-indigo-600 block mt-0.5">{{ number_format($srv->price, 0, ',', '.') }} đ</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                                    <button @click="editServiceData = { id: '{{ $srv->id }}', name: '{{ $srv->name }}', price: '{{ $srv->price }}', category: '{{ $srv->category }}' }; showEditServiceModal = true"
                                                            class="inline-flex items-center gap-1 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 text-indigo-750 px-2 py-1.5 rounded-xl font-bold text-[10px] transition active:scale-95">
                                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path></svg>
                                                        Sửa
                                                    </button>
                                                    <form action="{{ route('admin.services.destroy') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $srv->id }}">
                                                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này?')"
                                                                class="inline-flex items-center gap-1 bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-750 px-2 py-1.5 rounded-xl font-bold text-[10px] transition active:scale-95">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-slate-400 italic col-span-full pl-2">Chưa có dịch vụ trong loại này</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 6. NOTES TAB -->
                <div x-show="activeTab === 'notes'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
                    <!-- Form card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm h-fit">
                        <h3 class="text-base font-black text-slate-800 mb-4 uppercase tracking-wider">Thêm Ghi Chú</h3>
                        
                        <form action="{{ route('admin.notes.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label for="note_title" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tiêu đề ghi chú</label>
                                <input type="text" name="title" id="note_title" required placeholder="Ví dụ: Giao ca tối, Nhắc nhở dọn dẹp"
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label for="note_content" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Nội dung chi tiết</label>
                                <textarea name="content" id="note_content" required rows="4" placeholder="Ví dụ: Bàn 3 hôm nay bị lỗi nút bấm..."
                                          class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition"></textarea>
                            </div>

                            <button type="submit" class="w-full theme-primary-bg theme-primary-hover-bg text-white font-bold py-3.5 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Lưu Ghi Chú
                            </button>
                        </form>
                    </div>

                    <!-- List card -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm lg:col-span-2 space-y-4">
                        <h3 class="text-base font-black text-slate-800 uppercase tracking-wider">Lịch Sử Ghi Chú Chi Nhánh</h3>
                        
                        <div class="space-y-4">
                            @forelse($notes as $note)
                                <div class="bg-slate-50/50 rounded-2xl p-5 border border-slate-100 space-y-3 relative">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-extrabold text-slate-800">{{ $note->title }}</h4>
                                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                                Người đăng: {{ $note->creator->name ?? 'Hệ thống' }} • {{ $note->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        
                                        <form action="{{ route('admin.notes.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ghi chú này?')" class="inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $note->id }}">
                                            <button type="submit" class="text-slate-450 hover:text-rose-600 transition p-1" title="Xóa ghi chú">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <p class="text-xs text-slate-650 leading-relaxed font-medium whitespace-pre-line">{{ $note->content }}</p>
                                </div>
                            @empty
                                <div class="text-center py-12 text-slate-400 italic">
                                    Chưa có ghi chú nào được đăng.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
            <!-- Modals start here -->

    <!-- Edit Table Modal -->
    <div x-show="showEditTableModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition duration-300 border border-slate-100" @click.stop>
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Sửa thông tin bàn</h3>
                <button @click="showEditTableModal = false" class="text-slate-400 hover:text-slate-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.tables.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="id" :value="editTableData.id">
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên bàn chơi</label>
                    <input type="text" name="name" required x-model="editTableData.name"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input type="checkbox" name="is_cafe" id="edit_is_cafe" x-model="editTableData.is_cafe" value="1"
                           class="w-5 h-5 rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 cursor-pointer accent-indigo-600">
                    <label for="edit_is_cafe" class="text-xs font-extrabold text-slate-700 select-none cursor-pointer">Đây là bàn Cafe (Miễn phí tiền giờ)</label>
                </div>

                <div class="space-y-1.5" x-show="!editTableData.is_cafe">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Đơn giá thuê (đồng/giờ)</label>
                    <input type="number" name="price_per_hour" x-model="editTableData.price_per_hour" min="0" :required="!editTableData.is_cafe"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showEditTableModal = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div x-show="showEditServiceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition duration-300 border border-slate-100" @click.stop>
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Sửa dịch vụ</h3>
                <button @click="showEditServiceModal = false" class="text-slate-400 hover:text-slate-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.services.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="id" :value="editServiceData.id">
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên món / dịch vụ</label>
                    <input type="text" name="name" required x-model="editServiceData.name"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Phân Loại Tab</label>
                    <select name="category" required x-model="editServiceData.category"
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        <option value="thức uống">Thức uống</option>
                        <option value="đồ ăn">Đồ ăn</option>
                        <option value="khác">Khác</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Đơn giá (đồng)</label>
                    <input type="number" name="price" required x-model="editServiceData.price" min="0"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-bold focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showEditServiceModal = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div x-data="{ show: false, emp: { id: '', name: '', username: '' } }" 
         @open-edit-employee.window="emp = $event.detail; show = true"
         x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition duration-300 border border-slate-100" @click.stop>
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Sửa nhân viên</h3>
                <button @click="show = false" class="text-slate-400 hover:text-slate-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form :action="`/admin/staff/${emp.id}`" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Họ và tên</label>
                    <input type="text" name="name" x-model="emp.name" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên đăng nhập (Username)</label>
                    <input type="text" name="username" x-model="emp.username" required
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Mật khẩu mới (Tùy chọn)</label>
                    <input type="text" name="password" placeholder="Để trống nếu không đổi mật khẩu" minlength="6"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="button" @click="show = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md theme-primary-glow-shadow transition active:scale-95">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Employee Modal -->
    <div x-data="{ show: false, emp: { id: '', name: '' } }"
         @open-delete-employee.window="emp = $event.detail; show = true"
         x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition duration-300 border border-slate-100" @click.stop>
            <div class="p-6 text-center space-y-4">
                <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-800">Xác nhận xóa</h3>
                <p class="text-sm text-slate-500 font-medium">Bạn có chắc chắn muốn xóa nhân viên <span class="font-bold text-slate-800" x-text="emp.name"></span> không? Hành động này không thể hoàn tác.</p>
                
                <form :action="`/admin/staff/${emp.id}`" method="POST" class="pt-2 flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="show = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md transition active:scale-95">
                        Xóa ngay
                    </button>
                </form>
            </div>
        </div>
    </div>
        <!-- History Invoice Modal -->
        <div x-show="showHistoryInvoiceModal" class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] max-md:pt-0 bg-black/60 backdrop-blur-sm overflow-y-auto p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl transition-all transform scale-100 flex flex-col max-md:rounded-t-3xl max-md:rounded-b-none" @click.away="showHistoryInvoiceModal = false">
                <div class="p-6 border-b border-emerald-100 bg-emerald-50 text-emerald-950 flex justify-between items-center">
                    <h3 class="text-base font-bold flex items-center gap-1.5 uppercase tracking-wide">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Hóa đơn thanh toán
                    </h3>
                    <button @click="showHistoryInvoiceModal = false" class="text-emerald-600 hover:text-emerald-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-sm">
                    <template x-if="selectedInvoice">
                        <div>
                            <div class="flex flex-col gap-1 text-slate-600 border-b border-slate-100 pb-3 mb-4">
                                <div class="flex justify-between items-center">
                                    <span>Bàn: <strong class="text-slate-800" x-text="selectedInvoice.table?.name"></strong></span>
                                    <span>Giờ chơi: <span class="font-mono text-emerald-600 font-bold" x-text="selectedInvoice.duration_minutes + ' phút'"></span></span>
                                </div>
                                <div class="flex justify-between items-center text-xs text-slate-500">
                                    <span>Vào: <strong x-text="formatTime(selectedInvoice.start_time)"></strong></span>
                                    <span>Ra: <strong x-text="formatTime(selectedInvoice.end_time)"></strong></span>
                                </div>
                            </div>
                                <div class="flex justify-between font-bold text-slate-700">
                                    <span>Tiền bàn bida:</span>
                                    <span class="text-slate-800" x-text="formatVND(selectedInvoice.table_fee || 0)"></span>
                                </div>
                                
                                <div class="border-t border-dashed border-slate-200 my-2 pt-2">
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Đồ ăn / Thức uống:</span>
                                    <div class="max-h-32 overflow-y-auto space-y-1 mt-1.5 pr-1">
                                        <template x-for="srv in (selectedInvoice.items || [])" :key="srv.id">
                                            <div class="flex justify-between text-xs text-slate-600 font-medium">
                                                <span x-text="srv.service_name + ' x' + srv.quantity"></span>
                                                <span x-text="formatVND(srv.subtotal)"></span>
                                            </div>
                                        </template>
                                        <template x-if="(selectedInvoice.items || []).length === 0">
                                            <span class="text-xs italic text-slate-400">Không gọi dịch vụ đi kèm</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex justify-between font-bold text-slate-700 border-b border-slate-100 pb-2">
                                    <span>Tiền nước uống tổng:</span>
                                    <span class="text-slate-800" x-text="formatVND(selectedInvoice.services_fee || 0)"></span>
                                </div>

                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-sm font-bold text-slate-800">Tổng cộng (Làm tròn):</span>
                                    <span class="text-lg font-black text-red-600" x-text="formatVND(selectedInvoice.total || 0)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50 flex flex-col gap-2 max-md:pb-10">
                    <div class="flex gap-2 justify-end">
                        <button type="button" @click="showHistoryInvoiceModal = false" 
                                class="bg-white hover:bg-slate-100 border text-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition active:scale-95">
                            Thoát
                        </button>
                        <a :href="'/invoices/' + (selectedInvoice?.id || 0) + '/print'" target="_blank"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition active:scale-95 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            In hóa đơn
                        </a>
                    </div>
                </div>
            </div>
        </div>
        </div> <!-- Close x-data wrapper -->
    </div>
</x-app-layout>
