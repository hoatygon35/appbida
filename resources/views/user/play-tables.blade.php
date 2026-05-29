<x-app-layout>
    <!-- Main Container -->
    <div class="py-6 sm:py-8" x-data="playManager()">
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

            <!-- Grid tables -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($tables as $table)
                    @php
                        $session = $activeSessions->get($table->id) ?? null;
                        $servicesFee = 0;
                        if ($session) {
                            foreach($session->services as $s) {
                                $servicesFee += $s->price * $s->pivot->quantity;
                            }
                        }
                    @endphp

                    @if($session)
                        <!-- Active Table Card (Amber-Orange-Rose Gradient) -->
                        <div class="bg-gradient-to-br from-amber-500 via-orange-600 to-rose-600 rounded-2xl p-4 text-white shadow-xl flex flex-col justify-between overflow-hidden relative active:scale-[0.99] transition-all duration-300 min-h-[190px] md:min-h-[210px] hover:shadow-2xl border border-white/10 animate-fade-in"
                             x-data="billiardClock('{{ $session->start_time }}', {{ $table->price_per_hour }}, {{ $servicesFee }})">
                            
                            <!-- Header Card -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-extrabold tracking-wide">{{ $table->name }}</h3>
                                    <p class="text-[11px] text-orange-100 font-semibold mt-0.5">
                                        @if($table->price_per_hour == 0)
                                            0đ/h
                                        @else
                                            {{ number_format($table->price_per_hour, 0, ',', '.') }}đ/h
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase bg-rose-500/30 border border-rose-400/30 text-rose-100 flex items-center gap-1 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                    Chơi
                                </span>
                            </div>

                            <!-- Body Card -->
                            <div class="my-2 flex-grow flex flex-col justify-center">
                                <div class="space-y-1.5">
                                    <div class="text-2xl font-black font-mono tracking-wider text-white" x-text="timer">00:00:00</div>
                                    <div class="text-[10px] bg-white/10 backdrop-blur-md border border-white/5 rounded-lg px-2 py-1 flex justify-between items-center font-bold text-white/90">
                                        <span>Dịch vụ:</span>
                                        <span>{{ count($session->services) }} món</span>
                                    </div>
                                    <div class="flex items-baseline gap-1 mt-1">
                                        <span class="text-[10px] text-orange-150 font-bold">Tạm tính:</span>
                                        <span class="text-sm font-black text-white" x-text="formatVND(fee + servicesFee)">0đ</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Card / Action Buttons -->
                            <div class="flex gap-2 pt-2 border-t border-white/10 mt-auto">
                                <button @click="openServiceModal({{ $session->id }}, {{ json_encode($session->services->map(function($s) { return ['id' => $s->id, 'name' => $s->name, 'price' => $s->price, 'category' => $s->category ?? '', 'pivot' => ['pivot_id' => $s->pivot->pivot_id ?? null, 'quantity' => $s->pivot->quantity, 'note' => $s->pivot->note ?? '']]; })->values()) }}, '{{ $session->start_time }}', {{ $table->price_per_hour }}, '{{ $table->name }}')" 
                                        class="flex-grow bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold py-2 px-2.5 rounded-xl transition duration-150 flex items-center justify-center gap-1 shadow-md uppercase tracking-wide border border-emerald-500/20 active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Xem bàn
                                </button>
                                <button @click="openChangeTableModal({{ $session->id }}, {{ $table->id }}, {{ $table->price_per_hour }})" 
                                        class="bg-white/15 hover:bg-white/25 text-white border border-white/10 text-xs font-bold p-2 rounded-xl transition duration-150 shadow-md active:scale-95"
                                        title="Chuyển bàn">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Available Table Card (Emerald-Teal Gradient) -->
                        <div class="bg-gradient-to-br from-emerald-500 via-teal-600 to-teal-700 rounded-2xl p-4 text-white shadow-xl flex flex-col justify-between overflow-hidden relative active:scale-[0.99] transition-all duration-300 min-h-[190px] md:min-h-[210px] hover:shadow-2xl border border-white/10">
                            
                            <!-- Header Card -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-base font-extrabold tracking-wide">{{ $table->name }}</h3>
                                    <p class="text-[11px] text-emerald-100 font-semibold mt-0.5">
                                        @if($table->price_per_hour == 0)
                                            0đ/h
                                        @else
                                            {{ number_format($table->price_per_hour, 0, ',', '.') }}đ/h
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase bg-white/20 border border-white/10 backdrop-blur-md text-white/95">
                                    Trống
                                </span>
                            </div>

                            <!-- Body Card -->
                            <div class="my-2 flex-grow flex flex-col justify-center items-center">
                                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/40 border border-white/5 shadow-inner">
                                    <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-[10px] text-white/60 font-bold uppercase tracking-wider mt-1">Sẵn sàng</p>
                            </div>

                            <!-- Footer Card / Action Buttons -->
                            <div class="pt-2 border-t border-white/10 mt-auto">
                                <button @click="openTable({{ $table->id }})" 
                                        class="w-full bg-white/15 hover:bg-white/25 border border-white/20 text-white text-[11px] font-bold py-2 px-3 rounded-xl transition duration-150 flex items-center justify-center gap-1 shadow-md active:scale-95 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                                    Mở bàn
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

            <!-- 1. MODAL CHI TIẾT & DỊCH VỤ ĐÃ GỌI (Modal 1) -->
        <div x-show="showServiceModal" class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] max-md:pt-0 bg-black/60 backdrop-blur-sm overflow-y-auto p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl transition-all transform scale-100 flex flex-col max-h-[85vh] max-md:rounded-t-3xl max-md:rounded-b-none max-md:max-h-[80vh]">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800 uppercase tracking-wide flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Chi tiết: <span x-text="activeTableName"></span>
                    </h3>
                    <button @click="showServiceModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-grow space-y-5">
                    <!-- Session Stopwatch & Current Fee Info -->
                    <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-100 grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <p class="text-indigo-400 font-bold uppercase tracking-wider">Bắt đầu</p>
                            <p class="text-sm font-black text-slate-800 mt-0.5" x-text="activeTableStartTime ? activeTableStartTime.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'}) : '--:--'"></p>
                        </div>
                        <div>
                            <p class="text-indigo-400 font-bold uppercase tracking-wider">Kết thúc</p>
                            <p class="text-sm font-black text-slate-800 mt-0.5" x-text="activeTableCurrentTime"></p>
                        </div>
                        <div class="col-span-2 sm:col-span-1 text-right">
                            <p class="text-indigo-400 font-bold uppercase tracking-wider">Tiền giờ</p>
                            <p class="text-lg font-black text-slate-800 mt-0.5" x-text="formatVND(activeTableFee)"></p>
                        </div>
                    </div>

                    <!-- Called Services List -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider">Dịch vụ đã gọi</h4>
                        </div>
                        
                        <div class="overflow-y-auto pr-1">
                            <table class="w-full text-left border-collapse mt-2">
                                <thead>
                                    <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                        <th class="py-2 px-3 rounded-l-lg">Tên dịch vụ</th>
                                        <th class="py-2 px-3 text-right">Đơn giá</th>
                                        <th class="py-2 px-3 text-center w-20">SL</th>
                                        <th class="py-2 px-3 text-center rounded-r-lg">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                                    <template x-for="(item, idx) in currentSessionServices" :key="idx">
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-2 px-3">
                                                <div class="font-bold text-slate-800" x-text="item.name"></div>
                                                <div x-show="item.pivot.note" class="text-[10px] text-indigo-500 font-semibold mt-0.5" x-text="'📝 ' + item.pivot.note"></div>
                                            </td>
                                            <td class="py-2 px-3 text-right font-semibold text-slate-600" x-text="formatVND(item.price)"></td>
                                            <td class="py-2 px-3 text-center">
                                                <input type="number" x-model.number="item.pivot.quantity" min="1" @focus="$el.select()" class="!w-16 text-center border border-slate-200 rounded-lg py-1 px-1 text-slate-800 font-bold focus:ring-2 focus:ring-indigo-400/20 focus:border-indigo-400 mx-auto inline-block shadow-sm">
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                <button type="button" @click="currentSessionServices.splice(idx, 1)" 
                                                        class="w-6 h-6 inline-flex items-center justify-center text-white bg-red-500 hover:bg-red-600 rounded-md shadow-sm transition active:scale-90" 
                                                        title="Xóa dịch vụ">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <template x-if="currentSessionServices.length === 0">
                                <p class="text-xs text-slate-450 italic text-center py-8 bg-slate-50/50 rounded-xl border border-dashed border-slate-200 mt-2">Chưa gọi dịch vụ nào cho bàn</p>
                            </template>

                            <div class="mt-5 pt-4 border-t border-slate-200 flex flex-col gap-2.5">
                                <div class="flex justify-between items-center text-sm font-bold text-slate-600 px-2">
                                    <span class="uppercase tracking-wider">Tiền dịch vụ:</span>
                                    <span x-text="formatVND(getServicesTotalFee())"></span>
                                </div>
                                <div class="flex justify-between items-center text-base font-black text-red-600 bg-red-50/50 p-3 rounded-xl border border-red-100">
                                    <span class="uppercase tracking-wider">Tổng tiền:</span>
                                    <span class="text-xl" x-text="formatVND(activeTableFee + getServicesTotalFee())"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-between items-center max-md:pb-10">
                    <button type="button" @click="openAddServiceModal()" 
                            class="inline-flex items-center gap-1 text-xs font-extrabold text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-3 py-2 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Thêm dịch vụ
                    </button>
                    <div class="flex gap-3">
                        <button @click="showServiceModal = false" class="bg-white hover:bg-slate-100 border text-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition active:scale-95">Đóng</button>
                        <button x-show="hasChangesInServices()" @click="saveSessionServicesInPlace()" class="theme-primary-bg theme-primary-hover-bg text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md theme-primary-glow-shadow transition active:scale-95">Lưu tất cả</button>
                        <button @click="triggerCheckoutFromModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition active:scale-95 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MODAL THÊM DỊCH VỤ MỚI (Modal 2 - Rộng hơn, có cột Ghi chú) -->
        <div x-show="showAddServiceModal" class="fixed inset-0 z-[60] flex items-start justify-center pt-[10vh] max-md:pt-0 bg-black/60 backdrop-blur-sm p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl transition-all transform scale-100 flex flex-col max-h-[85vh] max-md:rounded-t-3xl max-md:rounded-b-none">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Thêm dịch vụ</h3>
                    <button @click="showAddServiceModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Search input -->
                <div class="p-4 border-b border-slate-100 bg-white pb-3">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tìm kiếm dịch vụ</label>
                    <input type="text" x-model="searchServiceQuery" placeholder="Nhập tên dịch vụ..." 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                </div>

                <!-- Category Tabs Navigation -->
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex gap-2 items-center">
                    <button type="button" @click="activeServiceCategory = 'thức uống'"
                            :class="activeServiceCategory === 'thức uống' ? 'theme-primary-bg text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                            class="flex-1 py-2 px-3 text-xs font-extrabold rounded-xl transition duration-150 text-center flex items-center justify-center gap-1 active:scale-95">
                        🥤 Nước uống
                    </button>
                    <button type="button" @click="activeServiceCategory = 'đồ ăn'"
                            :class="activeServiceCategory === 'đồ ăn' ? 'theme-primary-bg text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                            class="flex-1 py-2 px-3 text-xs font-extrabold rounded-xl transition duration-150 text-center flex items-center justify-center gap-1 active:scale-95">
                        🍳 Đồ ăn
                    </button>
                    <button type="button" @click="activeServiceCategory = 'khác'"
                            :class="activeServiceCategory === 'khác' ? 'theme-primary-bg text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                            class="flex-1 py-2 px-3 text-xs font-extrabold rounded-xl transition duration-150 text-center flex items-center justify-center gap-1 active:scale-95">
                        ⚡ Khác
                    </button>
                </div>

                <!-- Services List Table với cột Ghi chú -->
                <div class="overflow-y-auto flex-grow">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider sticky top-0">
                                <th class="py-3 px-2 sm:px-4">Tên dịch vụ</th>
                                <th class="py-3 px-1.5 sm:px-3">Đơn giá</th>
                                <th class="py-3 px-1.5 sm:px-3 w-1/3">Ghi chú</th>
                                <th class="py-3 px-1.5 sm:px-3 text-center w-[60px]">Số lượng</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            <template x-for="item in filteredServices" :key="item.id">
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3 px-4 font-bold text-slate-800" x-text="item.name"></td>
                                    <td class="py-3 px-3 font-extrabold text-slate-600 whitespace-nowrap" x-text="formatVND(item.price)"></td>
                                    <td class="py-3 px-3">
                                        <input type="text" x-model="item.note" placeholder="Ghi chú..."
                                               class="w-full px-2 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 transition min-w-[100px]">
                                    </td>
                                    <td class="py-3 px-1.5 sm:px-3 text-center">
                                        <input type="number" x-model.number="item.qty" min="0" @focus="$el.select()" class="!w-[48px] sm:!w-[56px] text-center border border-slate-200 rounded-lg py-1 px-0.5 sm:px-1 text-slate-800 font-bold focus:ring-2 focus:ring-indigo-400/20 focus:border-indigo-400 mx-auto inline-block shadow-sm">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-between items-center max-md:pb-10">
                    <button type="button" @click="showAddServiceModal = false" class="bg-white hover:bg-slate-100 border text-slate-700 text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition">Đóng</button>
                    <button type="button" @click="addSelectedServicesToQueue()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition active:scale-95">Thêm dịch vụ</button>
                </div>
            </div>
        </div>

        <!-- 2. MODAL CHUYỂN BÀN (Bottom Sheet trên mobile) -->
        <div x-show="showChangeTableModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl transition-all transform scale-100 max-md:rounded-t-3xl max-md:rounded-b-none flex flex-col max-h-[80vh]">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-800 uppercase tracking-wide">Chọn bàn để chuyển</h3>
                    <button @click="showChangeTableModal = false" class="text-slate-400 hover:text-slate-600 p-1 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form id="changeTableForm" action="{{ route('play-tables.change-table') }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
                    @csrf
                    <input type="hidden" name="session_id" :value="changeTableSessionId">
                    <input type="hidden" name="new_table_id">
                    
                    <div class="p-5 overflow-y-auto space-y-3 flex-grow max-h-[50vh]">
                        <p class="text-xs text-slate-400 font-semibold mb-2">Danh sách bàn trống có cùng đơn giá giờ chơi:</p>
                        
                        <template x-for="t in allTablesList" :key="t.id">
                            <div x-show="!t.in_use && parseFloat(t.price) === parseFloat(changeTablePricePerHour)" 
                                 class="flex justify-between items-center bg-sky-50/50 border border-sky-100 rounded-2xl p-4 hover:bg-sky-100/50 transition">
                                <span class="text-xs font-bold text-slate-800" x-text="t.name + ' (' + formatVND(t.price) + '/giờ)'"></span>
                                <button type="button" @click="submitChangeTable(t.id)"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-sm transition active:scale-95">
                                    Chọn
                                </button>
                            </div>
                        </template>

                        <template x-if="!hasEmptyTableWithSamePrice()">
                            <div class="text-center py-8 px-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                                <p class="text-xs text-slate-400 italic font-bold">Không có bàn trống nào cùng đơn giá để chuyển đổi.</p>
                                <p class="text-[10px] text-slate-400 mt-1">* Chú ý: Chỉ có thể chuyển đổi tự động giữa các bàn có cùng giá giờ chơi.</p>
                            </div>
                        </template>
                    </div>

                    <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-start max-md:pb-10">
                        <button type="button" @click="showChangeTableModal = false" class="bg-white hover:bg-slate-100 border text-slate-700 text-xs font-bold py-2.5 px-5 rounded-xl shadow-sm transition active:scale-95">Hủy</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. MODAL XÁC NHẬN THANH TOÁN (Bottom Sheet trên mobile) -->
        <div x-show="showCheckoutModal" class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] max-md:pt-0 bg-black/60 backdrop-blur-sm overflow-y-auto p-4 max-md:items-end max-md:p-0" x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl transition-all transform scale-100 flex flex-col max-md:rounded-t-3xl max-md:rounded-b-none">
                <div class="p-6 border-b border-emerald-100 bg-emerald-50 text-emerald-950">
                    <h3 class="text-base font-bold text-center flex items-center justify-center gap-1.5 uppercase tracking-wide">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Hóa đơn thanh toán
                    </h3>
                </div>

                <div class="p-6 space-y-4 text-sm">
                    <div class="flex justify-between items-center text-slate-600 border-b border-slate-100 pb-2">
                        <span>Bàn: <strong class="text-slate-800" x-text="checkoutData.tableName"></strong></span>
                        <span>Giờ chơi: <span class="font-mono text-emerald-600 font-bold" x-text="checkoutData.timeElapsed"></span></span>
                    </div>

                    <div class="space-y-2.5">
                        <div class="flex justify-between font-bold text-slate-700">
                            <span>Tiền bàn bida:</span>
                            <span class="text-slate-800" x-text="formatVND(checkoutData.tableFee)"></span>
                        </div>
                        
                        <div class="border-t border-dashed border-slate-200 my-2 pt-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Đồ ăn / Thức uống:</span>
                            <div class="max-h-32 overflow-y-auto space-y-1 mt-1.5 pr-1">
                                <template x-for="srv in checkoutData.services" :key="srv.id">
                                    <div class="flex justify-between text-xs text-slate-600 font-medium">
                                        <span x-text="srv.name + ' x' + srv.pivot.quantity"></span>
                                        <span x-text="formatVND(srv.price * srv.pivot.quantity)"></span>
                                    </div>
                                </template>
                                <template x-if="checkoutData.services.length === 0">
                                    <span class="text-xs italic text-slate-400">Không gọi dịch vụ đi kèm</span>
                                </template>
                            </div>
                        </div>

                        <div class="flex justify-between font-bold text-slate-700 border-b border-slate-100 pb-2">
                            <span>Tiền nước uống tổng:</span>
                            <span class="text-slate-800" x-text="formatVND(checkoutData.servicesFee)"></span>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-slate-800">Tổng cộng (Làm tròn):</span>
                            <span class="text-lg font-black text-red-600" x-text="formatVND(checkoutData.totalFee)"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Checkout Modal -->
                <div class="p-6 border-t border-slate-100 bg-slate-50 flex flex-col gap-2 max-md:pb-10">
                    <div class="flex gap-1.5 sm:gap-2 justify-end w-full">
                        <button type="button" @click="showCheckoutModal = false" 
                                class="flex-1 sm:flex-none justify-center bg-white hover:bg-slate-100 border text-slate-700 text-[11px] sm:text-xs font-bold py-2 sm:py-2.5 px-2 sm:px-4 rounded-xl shadow-sm transition active:scale-95 whitespace-nowrap">
                            Thoát
                        </button>
                        @if(Auth::user()->role === 'admin')
                            <button type="button" @click="executeCheckoutBluetooth()" 
                                    class="flex-1 sm:flex-none justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] sm:text-xs font-bold py-2 sm:py-2.5 px-2 sm:px-4 rounded-xl shadow-md transition active:scale-95 flex items-center gap-1 sm:gap-1.5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                In & T.Toán
                            </button>
                        @endif
                        <button type="button" @click="executeCheckoutWithPrint()" 
                                class="flex-1 sm:flex-none justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] sm:text-xs font-bold py-2 sm:py-2.5 px-2 sm:px-4 rounded-xl shadow-md transition active:scale-95 flex items-center gap-1 sm:gap-1.5 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path></svg>
                            Xem H.Đơn
                        </button>
                        <button type="button" @click="executeCheckout()" 
                                class="flex-1 sm:flex-none justify-center bg-blue-600 hover:bg-blue-700 text-white text-[11px] sm:text-xs font-bold py-2 sm:py-2.5 px-2 sm:px-4 rounded-xl shadow-md transition active:scale-95 flex items-center gap-1 sm:gap-1.5 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Thanh toán
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Custom Client-side Float Toast -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-12 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-12 opacity-0"
             class="fixed bottom-5 right-5 z-[200] text-white font-extrabold px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 border border-white/10 max-w-sm"
             :class="toastType === 'success' ? 'bg-emerald-600' : 'bg-rose-600'"
             x-cloak>
            <div class="flex items-center gap-2">
                <template x-if="toastType === 'success'">
                    <svg class="w-5 h-5 flex-shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                    </svg>
                </template>
                <template x-if="toastType === 'error'">
                    <svg class="w-5 h-5 flex-shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </template>
                <span class="text-xs" x-text="toastMessage"></span>
            </div>
            <button @click="showToast = false" class="text-white/85 hover:text-white transition ml-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Bill Clocks and play actions manager via AJAX -->
    <script>
        // ESC/POS Encoder for Bluetooth thermal printers (strips accents for generic compatibility)
        class EscPosEncoder {
            constructor() {
                this.buffer = [];
            }
            initialize() {
                this.buffer.push(0x1B, 0x40); // ESC @
                return this;
            }
            alignCenter() {
                this.buffer.push(0x1B, 0x61, 0x01); // ESC a 1
                return this;
            }
            alignLeft() {
                this.buffer.push(0x1B, 0x61, 0x00); // ESC a 0
                return this;
            }
            alignRight() {
                this.buffer.push(0x1B, 0x61, 0x02); // ESC a 2
                return this;
            }
            bold(on) {
                this.buffer.push(0x1B, 0x45, on ? 1 : 0); // ESC E on/off
                return this;
            }
            text(str) {
                const cleanStr = this.removeVietnameseAccents(str);
                for (let i = 0; i < cleanStr.length; i++) {
                    this.buffer.push(cleanStr.charCodeAt(i));
                }
                return this;
            }
            lineFeed() {
                this.buffer.push(0x0A);
                return this;
            }
            feed(lines) {
                this.buffer.push(0x1B, 0x64, lines);
                return this;
            }
            cut() {
                this.buffer.push(0x1D, 0x56, 0x41, 0x03);
                return this;
            }
            encode() {
                return new Uint8Array(this.buffer);
            }
            removeVietnameseAccents(str) {
                return str
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/đ/g, "d")
                    .replace(/Đ/g, "D");
            }
        }

        document.addEventListener('alpine:init', () => {
            // Billiard Clock script
            Alpine.data('billiardClock', (startStr, pricePerHour, servicesFee = 0) => ({
                startTime: startStr ? new Date(startStr) : null,
                timer: '00:00:00',
                fee: 0,
                servicesFee: servicesFee,
                interval: null,
                init() {
                    if (this.startTime) {
                        this.calculate();
                        this.interval = setInterval(() => this.calculate(), 1000);
                    }
                },
                destroy() {
                    if (this.interval) clearInterval(this.interval);
                },
                calculate() {
                    if (!this.startTime) return;
                    const now = new Date();
                    const diffMs = Math.abs(now - this.startTime);
                    const diffHours = diffMs / 3600000;
                    
                    const hh = Math.floor(diffMs / 3600000).toString().padStart(2, '0');
                    const mm = Math.floor((diffMs % 3600000) / 60000).toString().padStart(2, '0');
                    const ss = Math.floor((diffMs % 60000) / 1000).toString().padStart(2, '0');
                    
                    this.timer = `${hh}:${mm}:${ss}`;
                    const rawFee = diffHours * pricePerHour;
                    this.fee = Math.round(rawFee / 1000) * 1000;
                },
                formatVND(value) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                }
            }));

            // Dashboard Play Manager State
            Alpine.data('playManager', () => ({
                showServiceModal: false,
                showAddServiceModal: false,
                showChangeTableModal: false,
                showCheckoutModal: false,
                
                changeTableSessionId: '',
                changeTablePricePerHour: 0,
                activeSessionId: null,
                currentSessionServices: [],
                originalSessionServices: [],

                // Active table details
                activeTableName: '',
                activeTableStartTime: null,
                activeTablePrice: 0,
                activeTableTimer: '00:00:00',
                activeTableCurrentTime: '--:--',
                activeTableFee: 0,
                activeTableInterval: null,

                // Services listing for bulk addition
                searchServiceQuery: '',
                activeServiceCategory: 'thức uống',
                allServicesList: {!! json_encode($services->map(function($s) { return ['id' => $s->id, 'name' => $s->name, 'price' => $s->price, 'category' => $s->category, 'qty' => 0, 'note' => '']; })->values()) !!},
                allTablesList: {!! json_encode($tables->map(function($t) use ($activeSessions) { return ['id' => $t->id, 'name' => $t->name, 'price' => floatval($t->price_per_hour), 'in_use' => $activeSessions->has($t->id)]; })->values()) !!},

                showToast: false,
                toastMessage: '',
                toastType: 'success',

                triggerToast(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 4000);
                },
                
                checkoutData: {
                    sessionId: null,
                    tableName: '',
                    timeElapsed: '',
                    tableFee: 0,
                    servicesFee: 0,
                    totalFee: 0,
                    services: []
                },

                formatVND(value) {
                    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                },

                ajaxRequest(url, data) {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    }).then(res => res.json());
                },

                openTable(tableId) {
                    this.ajaxRequest("{{ route('play-tables.open') }}", { table_id: tableId })
                        .then(res => {
                            if (res.success) {
                                window.location.reload();
                            } else {
                                this.triggerToast(res.error || 'Có lỗi xảy ra!', 'error');
                            }
                        });
                },

                startActiveTableClock(startTimeStr, pricePerHour) {
                    if (this.activeTableInterval) clearInterval(this.activeTableInterval);
                    this.activeTableStartTime = startTimeStr ? new Date(startTimeStr) : null;
                    this.activeTablePrice = pricePerHour;
                    if (this.activeTableStartTime) {
                        this.calculateActiveTableFee();
                        this.activeTableInterval = setInterval(() => this.calculateActiveTableFee(), 1000);
                    }
                },

                stopActiveTableClock() {
                    if (this.activeTableInterval) {
                        clearInterval(this.activeTableInterval);
                        this.activeTableInterval = null;
                    }
                },

                getServicesTotalFee() {
                    let total = 0;
                    this.currentSessionServices.forEach(s => {
                        total += (s.price * s.pivot.quantity);
                    });
                    return total;
                },

                calculateActiveTableFee() {
                    if (!this.activeTableStartTime) return;
                    const now = new Date();
                    const diffMs = Math.abs(now - this.activeTableStartTime);
                    const diffHours = diffMs / 3600000;
                    
                    const hh = Math.floor(diffMs / 3600000).toString().padStart(2, '0');
                    const mm = Math.floor((diffMs % 3600000) / 60000).toString().padStart(2, '0');
                    const ss = Math.floor((diffMs % 60000) / 1000).toString().padStart(2, '0');
                    this.activeTableTimer = `${hh}:${mm}:${ss}`;
                    this.activeTableCurrentTime = now.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                    
                    const rawFee = diffHours * this.activeTablePrice;
                    this.activeTableFee = Math.round(rawFee / 1000) * 1000;
                },

                openServiceModal(sessionId, services, startTimeStr, pricePerHour, tableName) {
                    this.activeSessionId = sessionId;
                    this.activeTableName = tableName;
                    this.currentSessionServices = JSON.parse(JSON.stringify(services)); // Clone services list
                    this.originalSessionServices = JSON.parse(JSON.stringify(services)); // Clone original services list
                    this.startActiveTableClock(startTimeStr, pricePerHour);
                    this.showServiceModal = true;
                },

                openAddServiceModal() {
                    this.allServicesList.forEach(s => s.qty = 0);
                    this.searchServiceQuery = '';
                    this.activeServiceCategory = 'thức uống';
                    this.showAddServiceModal = true;
                },

                get filteredServices() {
                    const q = this.searchServiceQuery.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    return this.allServicesList.filter(s => {
                        // Match active category tab
                        const sCat = s.category || 'thức uống';
                        const matchesCategory = sCat.toLowerCase() === this.activeServiceCategory.toLowerCase();
                        if (!matchesCategory) return false;

                        // Match search query
                        if (!q) return true;
                        const name = s.name.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        return name.includes(q);
                    });
                },

                addSelectedServicesToQueue() {
                    let hasAdditions = false;
                    this.allServicesList.forEach(item => {
                        if (item.qty > 0) {
                            hasAdditions = true;
                            const note = (item.note || '').trim();
                            // Same service + same note -> merge
                            let found = this.currentSessionServices.find(s =>
                                s.id === item.id && (s.pivot.note || '') === note
                            );
                            if (found) {
                                found.pivot.quantity = parseInt(found.pivot.quantity) + item.qty;
                            } else {
                                this.currentSessionServices.push({
                                    id: item.id,
                                    name: item.name,
                                    price: item.price,
                                    category: item.category,
                                    pivot: { quantity: item.qty, note: note, pivot_id: null }
                                });
                            }
                        }
                    });
                    this.showAddServiceModal = false;
                    this.allServicesList.forEach(s => { s.qty = 0; s.note = ''; });

                    if (hasAdditions) {
                        this.saveSessionServices();
                    }
                },

                // Lưu và reload trang (dùng sau khi thêm dịch vụ mới)
                saveSessionServices() {
                    const servicesPayload = this.currentSessionServices.map(s => ({
                        id: s.id,
                        quantity: s.pivot.quantity,
                        note: s.pivot.note || ''
                    }));

                    this.ajaxRequest("{{ route('play-tables.sync-services') }}", {
                        session_id: this.activeSessionId,
                        services: servicesPayload
                    }).then(res => {
                        if (res.success) {
                            this.triggerToast('Lưu thông tin dịch vụ thành công!', 'success');
                            this.showServiceModal = false;
                            this.stopActiveTableClock();
                            window.location.reload();
                        } else {
                            this.triggerToast('Lỗi lưu dịch vụ', 'error');
                        }
                    });
                },

                // Lưu tại chỗ (không đóng modal, không reload) - dùng trong "Xem bàn"
                saveSessionServicesInPlace() {
                    const servicesPayload = this.currentSessionServices.map(s => ({
                        id: s.id,
                        quantity: s.pivot.quantity,
                        note: s.pivot.note || ''
                    }));

                    this.ajaxRequest("{{ route('play-tables.sync-services') }}", {
                        session_id: this.activeSessionId,
                        services: servicesPayload
                    }).then(res => {
                        if (res.success) {
                            // Cập nhật lại original để hide nút lưu, và tính lại hóa đơn
                            this.originalSessionServices = JSON.parse(JSON.stringify(this.currentSessionServices));
                            this.triggerToast('Lưu thành công!', 'success');
                        } else {
                            this.triggerToast('Lỗi lưu dịch vụ', 'error');
                        }
                    });
                },

                hasChangesInServices() {
                    // Check if length differs (deletions or additions)
                    if (this.currentSessionServices.length !== this.originalSessionServices.length) {
                        return true;
                    }
                    // Check each item by index for quantity changes
                    for (let i = 0; i < this.currentSessionServices.length; i++) {
                        const cur = this.currentSessionServices[i];
                        const orig = this.originalSessionServices[i];
                        if (!orig || parseInt(cur.pivot.quantity) !== parseInt(orig.pivot.quantity)) {
                            return true;
                        }
                    }
                    return false;
                },

                triggerCheckoutFromModal() {
                    const checkoutSessionId = this.activeSessionId;
                    const checkoutTableName = this.activeTableName;
                    const checkoutTableFee = this.activeTableFee;
                    const checkoutServices = this.currentSessionServices;
                    const checkoutStartTimeStr = this.activeTableStartTime;

                    this.showServiceModal = false;
                    this.stopActiveTableClock();

                    setTimeout(() => {
                        this.confirmCheckout(
                            checkoutSessionId,
                            checkoutTableName,
                            checkoutTableFee,
                            checkoutServices,
                            checkoutStartTimeStr
                        );
                    }, 150);
                },

                openChangeTableModal(sessionId, tableId, pricePerHour) {
                    this.changeTableSessionId = sessionId;
                    this.changeTablePricePerHour = pricePerHour;
                    this.showChangeTableModal = true;
                },

                hasEmptyTableWithSamePrice() {
                    return this.allTablesList.some(t => !t.in_use && parseFloat(t.price) === parseFloat(this.changeTablePricePerHour));
                },

                submitChangeTable(newTableId) {
                    const form = document.getElementById('changeTableForm');
                    form.querySelector('input[name="new_table_id"]').value = newTableId;
                    form.submit();
                },

                confirmCheckout(sessionId, tableName, tableFee, services, startTimeStr) {
                    const now = new Date();
                    const start = new Date(startTimeStr);
                    const diffMs = Math.abs(now - start);
                    
                    const hh = Math.floor(diffMs / 3600000).toString().padStart(2, '0');
                    const mm = Math.floor((diffMs % 3600000) / 60000).toString().padStart(2, '0');
                    const ss = Math.floor((diffMs % 60000) / 1000).toString().padStart(2, '0');
                    
                    let servicesFee = 0;
                    services.forEach(s => {
                        servicesFee += s.price * s.pivot.quantity;
                    });

                    this.checkoutData = {
                        sessionId: sessionId,
                        tableName: tableName,
                        timeElapsed: `${hh}:${mm}:${ss}`,
                        tableFee: tableFee,
                        servicesFee: servicesFee,
                        totalFee: tableFee + servicesFee,
                        services: services
                    };
                    
                    this.showCheckoutModal = true;
                },

                executeCheckout() {
                    this.ajaxRequest("{{ route('play-tables.checkout') }}", { session_id: this.checkoutData.sessionId })
                        .then(res => {
                            if (res.success) {
                                // Đóng bàn và tải lại trang, không mở trang in
                                window.location.reload();
                            } else {
                                this.triggerToast('Lỗi thanh toán', 'error');
                            }
                        });
                },

                executeCheckoutWithPrint() {
                    this.ajaxRequest("{{ route('play-tables.checkout') }}", { session_id: this.checkoutData.sessionId })
                        .then(res => {
                            if (res.success) {
                                // Mở trang in hóa đơn trong tab mới
                                const printUrl = `{{ url('/invoices') }}/${res.invoice_id}/print`;
                                window.open(printUrl, '_blank');
                                // Tải lại trang chính
                                window.location.reload();
                            } else {
                                this.triggerToast('Lỗi thanh toán', 'error');
                            }
                        });
                },

                async executeCheckoutBluetooth() {
                    if (!navigator.bluetooth) {
                        this.triggerToast("Trình duyệt này không hỗ trợ Web Bluetooth API. Hãy sử dụng Google Chrome hoặc Microsoft Edge.", 'error');
                        return;
                    }
                    
                    let device;
                    try {
                        device = await navigator.bluetooth.requestDevice({
                            acceptAllDevices: true,
                            optionalServices: [
                                '000018f0-0000-1000-8000-00805f9b34fb', // ESC/POS Printer
                                '00001101-0000-1000-8000-00805f9b34fb', // SPP
                                '0000e914-0000-1000-8000-00805f9b34fb',
                                '0000ff00-0000-1000-8000-00805f9b34fb'
                            ]
                        });
                    } catch (err) {
                        console.log('Bluetooth request cancelled', err);
                        return;
                    }

                    this.ajaxRequest("{{ route('play-tables.checkout') }}", { session_id: this.checkoutData.sessionId })
                        .then(async res => {
                            if (res.success) {
                                await this.printToDevice(device, this.checkoutData);
                                window.location.reload();
                            } else {
                                this.triggerToast('Lỗi thanh toán trên hệ thống', 'error');
                            }
                        });
                },

                async printToDevice(device, checkoutData) {
                    try {
                        console.log('Connecting to GATT Server...');
                        const server = await device.gatt.connect();
                        
                        console.log('Getting Service...');
                        let service;
                        const servicesList = [
                            '000018f0-0000-1000-8000-00805f9b34fb',
                            '00001101-0000-1000-8000-00805f9b34fb',
                            '0000e914-0000-1000-8000-00805f9b34fb',
                            '0000ff00-0000-1000-8000-00805f9b34fb'
                        ];
                        
                        for (let uuid of servicesList) {
                            try {
                                service = await server.getPrimaryService(uuid);
                                if (service) break;
                            } catch (e) {}
                        }
                        
                        if (!service) {
                            const primaryServices = await server.getPrimaryServices();
                            if (primaryServices.length > 0) {
                                service = primaryServices[0];
                            } else {
                                throw new Error('Could not find primary service on device');
                            }
                        }

                        console.log('Getting Characteristic...');
                        const characteristics = await service.getCharacteristics();
                        const characteristic = characteristics.find(c => c.properties.write || c.properties.writeWithoutResponse);
                        
                        if (!characteristic) {
                            throw new Error('No write characteristic found on device');
                        }

                        console.log('Encoding Esc/Pos receipt...');
                        const encoder = new EscPosEncoder();
                        encoder.initialize()
                            .alignCenter()
                            .bold(true)
                            .text("QUAN LY CAFE-BILLARDS").lineFeed()
                            .bold(false)
                            .text("Hoa Don Thanh Toan").lineFeed()
                            .text("--------------------------------").lineFeed()
                            .alignLeft()
                            .text(`Ban choi: ${checkoutData.tableName}`).lineFeed()
                            .text(`Thoi gian: ${checkoutData.timeElapsed}`).lineFeed()
                            .text("--------------------------------").lineFeed();

                        checkoutData.services.forEach(s => {
                            const qtyText = ` x${s.pivot.quantity}`;
                            const priceText = `${(s.price * s.pivot.quantity).toLocaleString('vi-VN')}d`;
                            const label = s.name.substring(0, 16);
                            const spaces = 32 - label.length - qtyText.length - priceText.length;
                            const line = label + qtyText + " ".repeat(Math.max(1, spaces)) + priceText;
                            encoder.text(line).lineFeed();
                        });

                        if (checkoutData.services.length > 0) {
                            encoder.text("--------------------------------").lineFeed();
                        }

                        encoder.text(`Tien gio: ${checkoutData.tableFee.toLocaleString('vi-VN')}d`).lineFeed();
                        encoder.text(`Tien nuoc: ${checkoutData.servicesFee.toLocaleString('vi-VN')}d`).lineFeed();
                        encoder.text("--------------------------------").lineFeed()
                            .bold(true)
                            .text(`Tong cong: ${checkoutData.totalFee.toLocaleString('vi-VN')}d`).lineFeed()
                            .bold(false)
                            .alignCenter()
                            .lineFeed()
                            .text("Cam on quy khach, hen gap lai!")
                            .lineFeed()
                            .feed(4)
                            .cut();

                        console.log('Writing bytes...');
                        const data = encoder.encode();
                        const chunkSize = 20;
                        for (let i = 0; i < data.length; i += chunkSize) {
                            const chunk = data.slice(i, i + chunkSize);
                            await characteristic.writeValue(chunk);
                            await new Promise(resolve => setTimeout(resolve, 50));
                        }
                        
                        console.log('Done printing!');
                        await device.gatt.disconnect();
                        return true;
                    } catch (error) {
                        console.error('Bluetooth error:', error);
                        this.triggerToast('Lỗi in bluetooth: ' + error.message, 'error');
                        return false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
