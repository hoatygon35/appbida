@php
    $currentTab = request()->get('tab', 'dashboard');
    $isOnDashboard = request()->routeIs('admin.dashboard');
    $isOnPlayTables = request()->routeIs('play-tables.index');
    $isOnDebts = request()->routeIs('admin.debts.index');
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-2.5 shadow-sm flex overflow-x-auto gap-3 md:gap-4 scrollbar-none items-center">
        @if(Auth::user()->role === 'admin')

            {{-- 1. Dashboard --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'dashboard'"
                   :class="activeTab === 'dashboard' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Dashboard
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'dashboard']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Dashboard
                </a>
            @endif

            {{-- 2. Bàn chơi --}}
            <a href="{{ route('play-tables.index') }}"
               class="{{ $isOnPlayTables ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                Bàn chơi
            </a>

            {{-- 3. Quản lý bàn --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'tables'"
                   :class="activeTab === 'tables' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Quản lý bàn
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'tables']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Quản lý bàn
                </a>
            @endif

            {{-- 4. Dịch vụ --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'services'"
                   :class="activeTab === 'services' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Dịch vụ
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'services']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Dịch vụ
                </a>
            @endif

            {{-- 5. Nhân viên --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'staff'"
                   :class="activeTab === 'staff' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Nhân viên
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'staff']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Nhân viên
                </a>
            @endif

            {{-- 6. Hóa đơn --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'invoices'"
                   :class="activeTab === 'invoices' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Hóa đơn
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'invoices']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Hóa đơn
                </a>
            @endif

            {{-- 7. Khách nợ --}}
            <a href="{{ route('admin.debts.index') }}"
               class="{{ $isOnDebts ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                Khách nợ
            </a>

            {{-- 8. Sổ ghi chú --}}
            @if($isOnDashboard)
                <a href="#" @click.prevent="activeTab = 'notes'"
                   :class="activeTab === 'notes' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                   class="flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Sổ ghi chú
                </a>
            @else
                <a href="{{ route('admin.dashboard', ['tab' => 'notes']) }}"
                   class="text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                    Sổ ghi chú
                </a>
            @endif

        @else
            {{-- Restricted tabs for Staff (user role) --}}

            {{-- 1. Bàn chơi --}}
            <a href="{{ route('play-tables.index') }}"
               class="{{ $isOnPlayTables ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                Bàn chơi
            </a>

            {{-- 2. Hóa đơn --}}
            <a href="{{ route('admin.dashboard', ['tab' => 'invoices']) }}"
               class="{{ $currentTab === 'invoices' && $isOnDashboard ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-1.5 px-5 py-2.5 rounded-2xl text-xs font-bold transition flex-shrink-0">
                Hóa đơn của tôi
            </a>
        @endif
    </div>
</div>
