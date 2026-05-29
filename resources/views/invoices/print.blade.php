@extends('layouts.print')

@section('content')
<div class="text-center font-sans">
    {{-- Tên CLB (từ database) --}}
    <h2 class="text-2xl font-bold uppercase tracking-wider mb-1">{{ $invoice->club->name ?? 'BIDA WIN' }}</h2>

    {{-- Địa chỉ - chỉ hiển thị nếu có dữ liệu --}}
    @if($invoice->club && $invoice->club->address)
        <p class="text-xs text-gray-600">Địa chỉ: {{ $invoice->club->address }}</p>
    @endif

    {{-- SĐT - chỉ hiển thị nếu có dữ liệu --}}
    @if($invoice->club && $invoice->club->phone)
        <p class="text-xs text-gray-600">SĐT: {{ $invoice->club->phone }}</p>
    @endif

    <hr class="border-t border-dashed border-gray-400 my-3">
    
    <h3 class="text-lg font-bold uppercase mb-2">Hóa Đơn Thanh Toán</h3>
    <div class="text-left text-xs space-y-1 mb-3">
        <p><strong>Bàn chơi:</strong> {{ $invoice->table->name ?? 'Bàn bida' }}</p>
        <p><strong>Giờ bắt đầu:</strong> {{ $invoice->start_time->format('d/m/Y H:i:s') }}</p>
        <p><strong>Giờ kết thúc:</strong> {{ $invoice->end_time->format('d/m/Y H:i:s') }}</p>
        <p><strong>Thời gian chơi:</strong> {{ $invoice->duration_minutes }} phút</p>
        <p><strong>Thu ngân:</strong> {{ $invoice->employee->name ?? '' }}</p>
    </div>

    <hr class="border-t border-dashed border-gray-400 my-3">

    <table class="w-full text-xs text-left mb-3">
        <thead>
            <tr class="font-bold border-b border-dashed border-gray-400">
                <th class="pb-1">Dịch vụ</th>
                <th class="text-right pb-1">SL</th>
                <th class="text-right pb-1">Đ.Giá</th>
                <th class="text-right pb-1">T.Tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td class="py-1">{{ $item->service_name }}</td>
                <td class="text-right py-1">{{ $item->quantity }}</td>
                <td class="text-right py-1">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                <td class="text-right py-1">{{ number_format($item->subtotal, 0, ',', '.') }}đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="border-t border-dashed border-gray-400 my-3">

    <div class="text-right text-xs space-y-1 mb-4">
        <p>Tiền giờ bida: <strong>{{ number_format($invoice->table_fee, 0, ',', '.') }}đ</strong></p>
        <p>Tiền dịch vụ: <strong>{{ number_format($invoice->services_fee, 0, ',', '.') }}đ</strong></p>
        <hr class="border-t border-dashed border-gray-400 my-1">
        <p class="text-base font-bold">Tổng cộng: <span class="text-red-600">{{ number_format($invoice->total, 0, ',', '.') }}đ</span></p>
    </div>

    {{-- Mã QR thanh toán --}}
    @if($invoice->club && $invoice->club->qr_code)
        <hr class="border-t border-dashed border-gray-400 my-3">
        <div class="my-3 text-center">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-2">Quét mã chuyển khoản</p>
            <img src="{{ $invoice->club->qr_code }}" class="w-36 h-36 mx-auto border border-gray-200 rounded-lg p-1 bg-white" alt="Mã QR thanh toán">
        </div>
    @endif

    <div class="text-center text-xs italic text-gray-500 mt-4">
        <p>Cảm ơn quý khách, hẹn gặp lại!</p>
        <p class="mt-1 text-[10px]">{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Menu hành động (Không in ra giấy) --}}
    <div class="no-print flex gap-3 justify-center mt-8 pt-5 border-t border-gray-200">
        <button onclick="window.close(); setTimeout(() => history.back(), 100); setTimeout(() => window.location.href='/', 500);" class="px-5 py-2.5 bg-gray-500 text-white rounded-xl font-bold text-sm hover:bg-gray-600 transition shadow-sm active:scale-95">
            Thoát
        </button>
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-sm active:scale-95 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l2.612-2.62a1.5 1.5 0 000-2.12L6.72 6.47m11.16 7.35l-2.612-2.62a1.5 1.5 0 010-2.12l2.612-2.62M3.75 18h16.5M3.75 6h16.5M12 9.75V18"></path></svg>
            In hóa đơn
        </button>
    </div>
</div>
@endsection
