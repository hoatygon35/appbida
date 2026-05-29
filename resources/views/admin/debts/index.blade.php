<x-app-layout>
    <div class="py-6 sm:py-8" x-data="debtManager()">
        @include('layouts.admin-sub-header')
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-100" role="alert">
                    <span class="font-medium">Thành công!</span> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-2xl bg-red-50 border border-red-100" role="alert">
                    <span class="font-medium">Lỗi!</span> {{ session('error') }}
                </div>
            @endif

            <!-- Debt summary and record debt card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Record debt form -->
                <div class="bg-white rounded-2xl border p-6 shadow-sm md:col-span-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4 uppercase tracking-wide">Ghi Nhận Nợ Mới</h3>
                        
                        <form action="{{ route('admin.debts.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tên khách nợ:</label>
                                <input type="text" name="customer_name" required placeholder="Ví dụ: Anh Nguyễn Văn A"
                                       class="w-full border-slate-200 rounded-xl text-sm focus:theme-primary-border focus:ring focus:ring-opacity-20 theme-primary-ring font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số tiền nợ (đ):</label>
                                <input type="number" name="amount" required min="1000" step="1000" placeholder="Số tiền tối thiểu 1.000đ"
                                       class="w-full border-slate-200 rounded-xl text-sm focus:theme-primary-border focus:ring focus:ring-opacity-20 theme-primary-ring font-medium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ghi chú lý do:</label>
                                <textarea name="note" rows="3" placeholder="Ví dụ: Nợ tiền bida + nước uống ca chiều"
                                          class="w-full border-slate-200 rounded-xl text-sm focus:theme-primary-border focus:ring focus:ring-opacity-20 theme-primary-ring font-medium"></textarea>
                            </div>
                            <button type="submit" class="w-full theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 rounded-xl text-xs transition shadow-sm theme-primary-glow-shadow active:scale-95">
                                Ghi nhận khoản nợ
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Debts list -->
                <div class="bg-white rounded-2xl border shadow-sm md:col-span-2 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header and total debt counter -->
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                            <div>
                                <h3 class="text-base font-bold text-slate-800 uppercase tracking-wide">Danh Sách Khách Nợ</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Quản lý và thu hồi công nợ chi nhánh</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Tổng nợ chi nhánh:</span>
                                <span class="text-2xl font-black text-red-500">{{ number_format($totalDebt, 0, ',', '.') }}đ</span>
                            </div>
                        </div>

                        <!-- Desktop Table list (Visible on desktop) -->
                        <div class="overflow-x-auto hidden sm:block">
                            <table class="w-full text-sm text-left text-slate-500">
                                <thead class="text-xs text-slate-400 uppercase bg-slate-50/20 border-b border-slate-100 font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Khách hàng</th>
                                        <th class="px-6 py-4 text-right">Tổng tiền nợ</th>
                                        <th class="px-6 py-4 text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($debts as $debt)
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/20 transition">
                                            <td class="px-6 py-4 font-bold text-slate-800">{{ $debt->customer_name }}</td>
                                            <td class="px-6 py-4 text-right font-black text-red-500 text-base">{{ number_format($debt->total_debt, 0, ',', '.') }}đ</td>
                                            <td class="px-6 py-4 text-center">
                                                <button @click="openPayModal({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->total_debt }})"
                                                        class="theme-primary-light-bg theme-primary-text font-bold px-4 py-2 rounded-xl text-xs border border-transparent hover:theme-primary-border transition active:scale-95">
                                                    Thu hồi nợ
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">
                                                Không có khách nợ nào ở chi nhánh này.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card List (Visible on mobile) -->
                        <div class="sm:hidden p-4 space-y-3">
                            @forelse($debts as $debt)
                                <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 flex justify-between items-center active:scale-[0.99] transition">
                                    <div class="space-y-1">
                                        <h4 class="text-sm font-bold text-slate-800">{{ $debt->customer_name }}</h4>
                                        <p class="text-xs text-slate-400">Số nợ: <span class="font-extrabold text-red-500">{{ number_format($debt->total_debt, 0, ',', '.') }}đ</span></p>
                                    </div>
                                    <button @click="openPayModal({{ $debt->id }}, '{{ $debt->customer_name }}', {{ $debt->total_debt }})"
                                            class="theme-primary-bg text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm theme-primary-glow-shadow active:scale-95 transition">
                                        Trả nợ
                                    </button>
                                </div>
                            @empty
                                <div class="text-center text-slate-400 italic py-10">
                                    Không có khách nợ nào ở chi nhánh này.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TRANSACT REPAYMENT MODAL (Bottom Sheet on Mobile) -->
        <div x-show="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl transition-all transform scale-100 max-md:rounded-t-3xl max-md:rounded-b-none">
                <div class="p-5 border-b border-slate-100 bg-emerald-50 text-emerald-950">
                    <h3 class="text-base font-bold flex items-center gap-1.5 uppercase tracking-wide">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Thanh toán trả nợ
                    </h3>
                </div>

                <form action="{{ route('admin.debts.pay') }}" method="POST">
                    @csrf
                    <input type="hidden" name="debt_id" :value="payData.debtId">

                    <div class="p-5 space-y-4 text-sm">
                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 text-xs space-y-1">
                            <p class="text-slate-600">Khách nợ: <strong class="text-slate-800" x-text="payData.customerName"></strong></p>
                            <p class="text-slate-600">Tổng nợ hiện tại: <strong class="text-red-500 text-sm font-black" x-text="formatVND(payData.totalDebt)"></strong></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Số tiền trả nợ (đ):</label>
                            <input type="number" name="pay_amount" required min="1000" step="1000" :max="payData.totalDebt"
                                   class="w-full border-slate-200 rounded-xl text-sm focus:theme-primary-border focus:ring focus:ring-opacity-20 theme-primary-ring font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mật khẩu tài khoản Admin:</label>
                            <input type="password" name="admin_password" required placeholder="Nhập mật khẩu Admin để phê duyệt"
                                   class="w-full border-slate-200 rounded-xl text-sm focus:theme-primary-border focus:ring focus:ring-opacity-20 theme-primary-ring font-medium">
                            <p class="text-[9px] text-slate-400 mt-2 leading-relaxed">* Bảo mật: Thao tác thu nợ / xóa nợ bắt buộc phải được phê duyệt bằng mật khẩu của Admin chi nhánh.</p>
                        </div>
                    </div>

                    <div class="p-5 border-t border-slate-50 bg-slate-50/50 flex gap-2 justify-end max-md:pb-10">
                        <button type="button" @click="showPayModal = false" class="bg-white hover:bg-slate-100 border text-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition active:scale-95">Hủy</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition active:scale-95">Xác nhận trả nợ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('debtManager', () => ({
                showPayModal: false,
                payData: {
                    debtId: '',
                    customerName: '',
                    totalDebt: 0
                },
                formatVND(value) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                },
                openPayModal(debtId, customerName, totalDebt) {
                    this.payData = {
                        debtId: debtId,
                        customerName: customerName,
                        totalDebt: totalDebt
                    };
                    this.showPayModal = true;
                }
            }));
        });
    </script>
</x-app-layout>
