<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
            {{ __('Hệ thống quản lý Cafe/Bida') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ activeTab: 'dashboard', showClubModal: false, showAdminModal: false, showEditClubModal: false, showEditAdminModal: false, showExtendModal: false, selectedClubId: null, selectedClubName: '', editClubData: { id: '', name: '', phone: '', address: '' }, editAdminData: { id: '', name: '', username: '' }, extendData: { id: '', name: '', days: 30 } }">
            
            <!-- Toast Notifications -->
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 shadow-md flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 shadow-md flex items-center gap-2" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Sub Navigation Menu (Image style) -->
            <div class="bg-white rounded-2xl p-2 border border-slate-200 shadow-sm flex gap-4 overflow-x-auto scrollbar-none mb-6">
                <button @click="activeTab = 'dashboard'" 
                        :class="activeTab === 'dashboard' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex-shrink-0">
                    Dashboard
                </button>
                <button @click="activeTab = 'clubs'" 
                        :class="activeTab === 'clubs' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex-shrink-0">
                    Quán/CLB
                </button>
                <button @click="activeTab = 'admins'" 
                        :class="activeTab === 'admins' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex-shrink-0">
                    Quản trị viên
                </button>
                <button @click="activeTab = 'reports'" 
                        :class="activeTab === 'reports' ? 'theme-primary-bg text-white shadow-md theme-primary-glow-shadow' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold transition flex-shrink-0">
                    Báo cáo
                </button>
            </div>

            <!-- Tab Content 1: Dashboard -->
            <div x-show="activeTab === 'dashboard'" class="space-y-8" x-cloak>
                <h3 class="text-xl font-extrabold text-slate-800 leading-tight">Dashboard Quản Lý</h3>
                
                <!-- 3 Stats cards row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Green Card -->
                    <div class="bg-[#51a374] text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group h-36">
                        <div class="absolute -right-4 -bottom-4 text-white/10 opacity-20 transform scale-150 group-hover:scale-175 transition duration-500">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="text-right">
                            <span class="text-4xl font-black block tracking-wider">{{ $clubs->count() }}</span>
                            <span class="text-xs font-bold mt-1 uppercase tracking-wider block opacity-90">Quán/CLB</span>
                        </div>
                    </div>
                    <!-- Blue Card -->
                    <div class="bg-[#4a72e6] text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group h-36">
                        <div class="absolute -right-4 -bottom-4 text-white/10 opacity-20 transform scale-150 group-hover:scale-175 transition duration-500">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="text-right">
                            <span class="text-4xl font-black block tracking-wider">{{ App\Models\User::where('role', 'admin')->count() }}</span>
                            <span class="text-xs font-bold mt-1 uppercase tracking-wider block opacity-90">Admin</span>
                        </div>
                    </div>
                    <!-- Yellow Card -->
                    <div class="bg-[#dca134] text-white rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group h-36">
                        <div class="absolute -right-4 -bottom-4 text-white/10 opacity-20 transform scale-150 group-hover:scale-175 transition duration-500">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="text-right">
                            <span class="text-4xl font-black block tracking-wider">{{ App\Models\User::where('role', 'user')->count() }}</span>
                            <span class="text-xs font-bold mt-1 uppercase tracking-wider block opacity-90">Nhân viên</span>
                        </div>
                    </div>
                </div>

                <!-- Admin Management Mini-list -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-base font-extrabold text-slate-800 uppercase tracking-wide">Quản Lý Admin</h3>
                        <button @click="activeTab = 'admins'" class="theme-primary-bg theme-primary-hover-bg text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition active:scale-95 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                            Thêm Admin
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                    <th class="py-4 px-6">ID</th>
                                    <th class="py-4 px-6">Tên đăng nhập</th>
                                    <th class="py-4 px-6">Quán/CLB</th>
                                    <th class="py-4 px-6">Ngày tạo</th>
                                    <th class="py-4 px-6 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @php
                                    $allAdmins = App\Models\User::where('role', 'admin')->with('club')->get();
                                @endphp
                                @forelse($allAdmins as $admin)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 text-slate-400 font-bold">#{{ $admin->id }}</td>
                                        <td class="py-4 px-6 font-bold text-slate-800">{{ $admin->username }}</td>
                                        <td class="py-4 px-6">{{ $admin->club->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 text-xs text-slate-500">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <form action="{{ route('manager.clubs.admins.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản Admin này?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $admin->id }}">
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 hover:border-rose-200 font-bold px-3 py-1.5 rounded-lg text-xs transition active:scale-95">
                                                    Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic">Chưa có tài khoản Admin nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Club Mini-list -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-base font-extrabold text-slate-800 uppercase tracking-wide">Quán/CLB</h3>
                        <button @click="activeTab = 'clubs'" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold border border-indigo-100 transition active:scale-95">
                            Quản lý
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($clubs as $club)
                            <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 space-y-1">
                                <h4 class="text-sm font-extrabold text-slate-800 flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full theme-primary-bg"></span>
                                    {{ $club->name }}
                                </h4>
                                <p class="text-xs text-slate-500">SĐT: {{ $club->phone }}</p>
                                <p class="text-xs text-slate-400 font-medium">Địa chỉ: {{ $club->address }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic col-span-full text-center py-6">Chưa có chi nhánh Quán/CLB nào</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Tab Content 2: Clubs List -->
            <div x-show="activeTab === 'clubs'" class="space-y-8" x-cloak>
                <!-- Banner / Header for Mobile (App feel) -->
                <div class="md:hidden flex justify-between items-center bg-white p-4 rounded-3xl border border-slate-150 shadow-sm mb-4">
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-800">Hệ thống Quán/CLB</h1>
                        <p class="text-xs text-slate-500 font-medium">Quản lý chuỗi Cafe - Billiards</p>
                    </div>
                    <button @click="showClubModal = true" class="theme-primary-bg theme-primary-hover-bg text-white px-4 py-2 rounded-2xl text-xs font-bold shadow-md theme-primary-glow-shadow transition active:scale-95 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        Thêm Quán/CLB
                    </button>
                </div>

                <!-- Header for Desktop -->
                <div class="hidden md:flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Quản lý Quán/CLB</h3>
                        <p class="text-slate-500 font-medium text-xs mt-1">Quản lý và cấp quyền tài khoản Admin cho các chi nhánh Quán/CLB</p>
                    </div>
                    <button @click="showClubModal = true" class="theme-primary-bg theme-primary-hover-bg text-white px-5 py-3 rounded-2xl text-sm font-bold shadow-lg theme-primary-glow-shadow transition active:scale-95 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        Thêm Quán/CLB Mới
                    </button>
                </div>

                <!-- Clubs Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($clubs as $club)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col justify-between">
                            <!-- Club Card Header -->
                            <div class="p-6 pb-4 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-extrabold text-slate-800 leading-tight">{{ $club->name }}</h3>
                                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span>{{ $club->address }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="theme-primary-light-bg theme-primary-text px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                                Chi nhánh
                                            </span>
                                            @if($club->expiry_date)
                                                @php
                                                    $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($club->expiry_date)->startOfDay(), false);
                                                @endphp
                                                @if($daysLeft > 10)
                                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 rounded-full border border-emerald-100">Còn {{ $daysLeft }} ngày</span>
                                                @elseif($daysLeft >= 0)
                                                    <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 rounded-full border border-orange-100">Còn {{ $daysLeft }} ngày</span>
                                                @else
                                                    <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 rounded-full border border-red-100">Hết hạn</span>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <!-- Extend Club button -->
                                            <button @click="$dispatch('open-extend-club', { id: '{{ $club->id }}', name: '{{ addslashes($club->name) }}', days: 30 })"
                                                    class="text-emerald-500 hover:text-emerald-700 transition p-1" title="Gia hạn sử dụng">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </button>
                                            <!-- Edit Club button -->
                                            <button @click="editClubData = { id: {{ $club->id }}, name: '{{ addslashes($club->name) }}', phone: '{{ addslashes($club->phone) }}', address: '{{ addslashes($club->address) }}' }; showEditClubModal = true"
                                                    class="text-slate-400 hover:text-indigo-650 transition p-1" title="Sửa chi nhánh">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path></svg>
                                            </button>
                                            <!-- Delete Club button -->
                                            <form action="{{ route('manager.clubs.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Quán/CLB này? Việc này sẽ xóa toàn bộ dữ liệu bàn chơi, nhân viên, hóa đơn liên quan!')" class="inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $club->id }}">
                                                <button type="submit" class="text-slate-400 hover:text-red-655 transition p-1" title="Xóa chi nhánh">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Grid -->
                            <div class="px-6 py-4 bg-slate-50/50 grid grid-cols-2 gap-4 border-b border-slate-100 text-center">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wide">Số lượng bàn</span>
                                    <p class="text-xl font-black text-slate-700">{{ $club->tables_count }}</p>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wide">Số dịch vụ</span>
                                    <p class="text-xl font-black text-slate-700">{{ $club->services_count }}</p>
                                </div>
                            </div>

                            <!-- Admin Account Management -->
                            <div class="p-6 space-y-4 flex-grow flex flex-col justify-end">
                                @php
                                    $admin = $club->users->first();
                                @endphp

                                @if($admin)
                                    <div class="bg-indigo-50/40 rounded-2xl p-4 border border-indigo-100/50 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-indigo-600/10 flex items-center justify-center text-indigo-600">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tài khoản Quản trị</h4>
                                                    <p class="text-sm font-extrabold text-slate-800">{{ $admin->name }}</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-1">
                                                <!-- Edit Admin -->
                                                <button @click="$dispatch('open-edit-admin', { id: '{{ $admin->id }}', name: '{{ addslashes($admin->name) }}', username: '{{ addslashes($admin->username) }}', club_id: '{{ $admin->club_id }}' })"
                                                        class="text-indigo-600 hover:bg-indigo-100/60 p-1.5 rounded-lg transition" title="Sửa tài khoản Admin">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path></svg>
                                                </button>
                                                <!-- Delete Admin -->
                                                <form action="{{ route('manager.clubs.admins.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản Admin này?')" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $admin->id }}">
                                                    <button type="submit" class="text-rose-600 hover:bg-rose-100/60 p-1.5 rounded-lg transition" title="Xóa tài khoản Admin">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center text-xs pt-1 border-t border-indigo-100/30">
                                            <span class="text-slate-500 font-medium">Username:</span>
                                            <span class="font-bold text-indigo-700 bg-white px-2 py-0.5 rounded-lg border border-indigo-100 shadow-sm">{{ $admin->username }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-amber-50/40 rounded-2xl p-4 border border-amber-100/50 text-center space-y-3">
                                        <p class="text-xs text-amber-800 font-bold">Chưa tạo tài khoản Admin cho chi nhánh này</p>
                                        <button @click="selectedClubId = {{ $club->id }}; selectedClubName = '{{ $club->name }}'; showAdminModal = true" 
                                                class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2.5 px-4 rounded-xl text-xs font-bold shadow-md hover:shadow transition active:scale-95 flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"></path></svg>
                                            Cấp tài khoản Admin
                                        </button>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between text-xs text-slate-400 pt-2 font-medium">
                                    <span>Liên hệ: {{ $club->phone }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 border border-slate-200 text-center space-y-3">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Chưa có Quán/CLB nào</h3>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">Vui lòng nhấp vào nút "Thêm Quán/CLB Mới" để tạo chi nhánh đầu tiên của bạn.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab Content 3: Admins List -->
            <div x-show="activeTab === 'admins'" class="space-y-8" x-cloak>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider">Quản lý Quản trị viên</h3>
                        <p class="text-slate-500 font-medium text-xs mt-1">Cấp phát và điều chỉnh thông tin tài khoản Quản trị của từng Quán/CLB</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                                    <th class="py-4 px-6">ID</th>
                                    <th class="py-4 px-6">Người quản lý</th>
                                    <th class="py-4 px-6">Tên đăng nhập (Username)</th>
                                    <th class="py-4 px-6">Chi nhánh Quán/CLB</th>
                                    <th class="py-4 px-6 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                @forelse($allAdmins as $admin)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 text-slate-400 font-bold">#{{ $admin->id }}</td>
                                        <td class="py-4 px-6 font-bold text-slate-800">{{ $admin->name }}</td>
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">{{ $admin->username }}</span>
                                        </td>
                                        <td class="py-4 px-6">{{ $admin->club->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 text-center flex items-center justify-center gap-2">
                                            <button @click="editAdminData = { id: {{ $admin->id }}, name: '{{ addslashes($admin->name) }}', username: '{{ addslashes($admin->username) }}' }; showEditAdminModal = true"
                                                    class="bg-indigo-50 hover:bg-indigo-100 text-indigo-650 border border-indigo-100 font-bold px-3 py-1.5 rounded-lg text-xs transition active:scale-95">
                                                Sửa
                                            </button>
                                            <form action="{{ route('manager.clubs.admins.destroy') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản Admin này?')" class="inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $admin->id }}">
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 font-bold px-3 py-1.5 rounded-lg text-xs transition active:scale-95">
                                                    Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic">Chưa có tài khoản Admin nào</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Content 4: Reports -->
            <div x-show="activeTab === 'reports'" class="space-y-8" x-cloak>
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-wider mb-6">Báo Cáo Hệ Thống</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                        <h4 class="text-sm font-bold text-slate-800 uppercase border-b pb-2">Quy Mô Hệ Thống</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Tổng số chi nhánh Quán/CLB:</span>
                                <span class="font-extrabold text-slate-800">{{ $clubs->count() }} chi nhánh</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Tổng số bàn chơi:</span>
                                <span class="font-extrabold text-slate-800">{{ App\Models\Table::count() }} bàn</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Tổng số thực đơn dịch vụ:</span>
                                <span class="font-extrabold text-slate-800">{{ App\Models\Service::count() }} món</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500">Nhân viên toàn chuỗi:</span>
                                <span class="font-extrabold text-slate-800">{{ App\Models\User::where('role', 'user')->count() }} người</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4 flex flex-col justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 uppercase border-b pb-2">Hoạt động kinh doanh</h4>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">Hệ thống đang hoạt động ổn định trên nền tảng đám mây. Các báo cáo doanh thu chi tiết được quản lý trực tiếp theo từng chi nhánh ở tài khoản Admin tương ứng.</p>
                        </div>
                        <div class="pt-4 flex gap-2">
                            <div class="flex-1 bg-indigo-50 p-4 rounded-2xl text-center border border-indigo-100">
                                <span class="text-[10px] uppercase font-bold text-indigo-500 block">Hóa đơn hệ thống</span>
                                <span class="text-xl font-black text-indigo-700">{{ App\Models\Invoice::count() }}</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 p-4 rounded-2xl text-center border border-emerald-100">
                                <span class="text-[10px] uppercase font-bold text-emerald-600 block">Doanh thu tạm tính</span>
                                <span class="text-xl font-black text-emerald-700">{{ number_format(App\Models\Invoice::sum('total'), 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Add Club -->
            <div x-show="showClubModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                
                <div @click.stop 
                     class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition duration-300 border border-slate-100"
                     x-show="showClubModal"
                     x-transition:enter="transition ease-out duration-300 transform scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100 opacity-100"
                     x-transition:leave-end="scale-95 opacity-0">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="text-lg font-black text-slate-800">Thêm Quán/CLB Mới</h3>
                        <button @click="showClubModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('manager.clubs.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div class="space-y-1.5">
                            <label for="name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên Quán/CLB</label>
                            <input type="text" name="name" id="name" required placeholder="Ví dụ: Bida Win Buôn Ma Thuột"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="phone" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Số điện thoại liên hệ</label>
                            <input type="text" name="phone" id="phone" required placeholder="Ví dụ: 0795.112.233"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="address" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Địa chỉ chi tiết</label>
                            <input type="text" name="address" id="address" required placeholder="Ví dụ: 10 Nguyễn Trãi, BMT"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showClubModal = false"
                                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-4 rounded-xl text-sm transition active:scale-95">
                                Hủy bỏ
                            </button>
                            <button type="submit"
                                    class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Tạo Chi Nhánh
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal: Assign Admin -->
            <div x-show="showAdminModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                
                <div @click.stop 
                     class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition duration-300 border border-slate-100"
                     x-show="showAdminModal"
                     x-transition:enter="transition ease-out duration-300 transform scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100 opacity-100"
                     x-transition:leave-end="scale-95 opacity-0">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h3 class="text-lg font-black text-slate-800">Cấp Tài Khoản Admin</h3>
                            <p class="text-xs text-slate-500 font-medium" x-text="selectedClubName"></p>
                        </div>
                        <button @click="showAdminModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('manager.clubs.admins.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="club_id" x-model="selectedClubId">

                        <div class="space-y-1.5">
                            <label for="admin_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên Người Quản Trị</label>
                            <input type="text" name="name" id="admin_name" required placeholder="Ví dụ: Nguyễn Văn A"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="admin_username" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên đăng nhập (Username)</label>
                            <input type="text" name="username" id="admin_username" required placeholder="Ví dụ: admin_bmt"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="admin_password" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Mật khẩu ban đầu</label>
                            <input type="password" name="password" id="admin_password" required placeholder="Tối thiểu 6 ký tự"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showAdminModal = false"
                                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-4 rounded-xl text-sm transition active:scale-95">
                                Hủy bỏ
                            </button>
                            <button type="submit"
                                    class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Cấp Tài Khoản
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal: Edit Club -->
            <div x-show="showEditClubModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                
                <div @click.stop 
                     class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition duration-300 border border-slate-100"
                     x-show="showEditClubModal"
                     x-transition:enter="transition ease-out duration-300 transform scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100 opacity-100"
                     x-transition:leave-end="scale-95 opacity-0">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="text-lg font-black text-slate-800">Sửa Quán/CLB</h3>
                        <button @click="showEditClubModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('manager.clubs.update') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="id" x-model="editClubData.id">

                        <div class="space-y-1.5">
                            <label for="edit_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên Quán/CLB</label>
                            <input type="text" name="name" id="edit_name" required x-model="editClubData.name"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit_phone" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Số điện thoại liên hệ</label>
                            <input type="text" name="phone" id="edit_phone" required x-model="editClubData.phone"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit_address" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Địa chỉ chi tiết</label>
                            <input type="text" name="address" id="edit_address" required x-model="editClubData.address"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showEditClubModal = false"
                                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-4 rounded-xl text-sm transition active:scale-95">
                                Hủy bỏ
                            </button>
                            <button type="submit"
                                    class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal: Edit Admin -->
            <div x-show="showEditAdminModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                
                <div @click.stop 
                     class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition duration-300 border border-slate-100"
                     x-show="showEditAdminModal"
                     x-transition:enter="transition ease-out duration-300 transform scale-95 opacity-0"
                     x-transition:enter-end="scale-100 opacity-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100 opacity-100"
                     x-transition:leave-end="scale-95 opacity-0">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h3 class="text-lg font-black text-slate-800">Sửa Tài Khoản Admin</h3>
                        </div>
                        <button @click="showEditAdminModal = false" class="text-slate-400 hover:text-slate-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('manager.clubs.admins.update') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="id" x-model="editAdminData.id">

                        <div class="space-y-1.5">
                            <label for="edit_admin_name" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên Người Quản Trị</label>
                            <input type="text" name="name" id="edit_admin_name" required x-model="editAdminData.name"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit_admin_username" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Tên đăng nhập (Username)</label>
                            <input type="text" name="username" id="edit_admin_username" required x-model="editAdminData.username"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="edit_admin_password" class="text-xs font-bold text-slate-500 uppercase tracking-wide">Mật khẩu mới (Bỏ trống nếu không muốn đổi)</label>
                            <input type="password" name="password" id="edit_admin_password" placeholder="Tối thiểu 6 ký tự"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="button" @click="showEditAdminModal = false"
                                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3 px-4 rounded-xl text-sm transition active:scale-95">
                                Hủy bỏ
                            </button>
                            <button type="submit"
                                    class="flex-1 theme-primary-bg theme-primary-hover-bg text-white font-bold py-3 px-4 rounded-xl text-sm shadow-md theme-primary-glow-shadow transition active:scale-95">
                                Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Extend Expiry Modal -->
    <div x-data="{ show: false, ext: { id: '', name: '', days: 30 } }" 
         @open-extend-club.window="ext = $event.detail; show = true"
         x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden transform transition duration-300 border border-slate-100" @click.stop>
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Gia hạn sử dụng</h3>
                <button @click="show = false" class="text-slate-400 hover:text-slate-650">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('manager.clubs.extend') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="id" x-model="ext.id">
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Chi nhánh</label>
                    <input type="text" x-model="ext.name" readonly
                           class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 font-medium focus:outline-none cursor-not-allowed">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Số ngày gia hạn</label>
                    <input type="number" name="days_to_add" x-model="ext.days" required min="1"
                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 font-medium focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition">
                    <p class="text-[10px] text-slate-500 font-medium mt-1">Gợi ý: 30 (1 tháng), 90 (3 tháng), 365 (1 năm)</p>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="show = false"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-650 font-bold py-3 px-4 rounded-xl text-xs transition active:scale-95">
                        Hủy bỏ
                    </button>
                    <button type="submit"
                            class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-4 rounded-xl text-xs shadow-md transition active:scale-95">
                        Gia hạn
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
