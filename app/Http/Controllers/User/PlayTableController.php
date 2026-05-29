<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Service;
use App\Models\GameSession;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayTableController extends Controller {
    public function index() {
        $clubId = auth()->user()->club_id;
        $tables = Table::where('club_id', $clubId)->get();
        $services = Service::where('club_id', $clubId)->get();

        // Load open sessions
        $openSessions = GameSession::where('club_id', $clubId)->where('status', 'open')->get()->keyBy('table_id');

        // Load all session_services rows (including duplicates with different notes)
        if ($openSessions->isNotEmpty()) {
            $sessionIds = $openSessions->pluck('id')->toArray();
            $allSessionServices = DB::table('session_services')
                ->join('services', 'services.id', '=', 'session_services.service_id')
                ->whereIn('session_services.session_id', $sessionIds)
                ->select(
                    'session_services.id as pivot_id',
                    'session_services.session_id',
                    'session_services.quantity',
                    'session_services.note',
                    'services.id',
                    'services.name',
                    'services.price',
                    'services.category'
                )
                ->get()
                ->groupBy('session_id');

            // Attach services to each session as a plain array
            foreach ($openSessions as $session) {
                $rows = $allSessionServices->get($session->id, collect());
                $session->setRelation('services', $rows->map(function($row) {
                    return (object)[
                        'id' => $row->id,
                        'name' => $row->name,
                        'price' => $row->price,
                        'category' => $row->category,
                        'pivot' => (object)[
                            'quantity' => $row->quantity,
                            'note' => $row->note,
                            'pivot_id' => $row->pivot_id,
                        ],
                    ];
                }));
            }
        }

        $activeSessions = $openSessions;
        return view('user.play-tables', compact('tables', 'services', 'activeSessions'));
    }

    // Mở bàn bida
    public function openTable(Request $request) {
        $request->validate(['table_id' => 'required|exists:tables,id']);

        $inUse = GameSession::where('table_id', $request->table_id)->where('status', 'open')->exists();
        if ($inUse) {
            return response()->json(['success' => false, 'error' => 'Bàn đang được sử dụng']);
        }

        $session = GameSession::create([
            'table_id' => $request->table_id,
            'club_id' => auth()->user()->club_id,
            'opened_by' => auth()->id(),
            'start_time' => now(),
            'status' => 'open'
        ]);

        return response()->json(['success' => true, 'session_id' => $session->id]);
    }

    // Gọi thêm dịch vụ (AJAX)
    public function addService(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1'
        ]);

        $session = GameSession::findOrFail($request->session_id);

        foreach ($request->services as $srv) {
            $note = $srv['note'] ?? '';
            // Same service + same note → merge quantity
            $existing = DB::table('session_services')
                ->where('session_id', $session->id)
                ->where('service_id', $srv['id'])
                ->where('note', $note)
                ->first();

            if ($existing) {
                DB::table('session_services')
                    ->where('id', $existing->id)
                    ->update(['quantity' => $existing->quantity + $srv['quantity'], 'updated_at' => now()]);
            } else {
                DB::table('session_services')->insert([
                    'session_id' => $session->id,
                    'service_id' => $srv['id'],
                    'quantity' => $srv['quantity'],
                    'note' => $note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // Sửa số lượng dịch vụ inline (AJAX)
    public function updateServiceQuantity(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $session = GameSession::findOrFail($request->session_id);
        $session->services()->updateExistingPivot($request->service_id, ['quantity' => $request->quantity]);

        return response()->json(['success' => true]);
    }

    // Xóa dịch vụ khỏi phiên (AJAX)
    public function deleteService(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'service_id' => 'required|exists:services,id'
        ]);

        $session = GameSession::findOrFail($request->session_id);
        $session->services()->detach($request->service_id);

        return response()->json(['success' => true]);
    }

    // Chuyển đổi bàn chơi (Yêu cầu kiểm tra cùng giá giá/giờ thuê)
    public function changeTable(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'new_table_id' => 'required|exists:tables,id'
        ]);

        $session = GameSession::findOrFail($request->session_id);
        $newTable = Table::findOrFail($request->new_table_id);

        if (GameSession::where('table_id', $request->new_table_id)->where('status', 'open')->exists()) {
            return back()->with('error', 'Bàn đích đang có khách!');
        }

        if ($session->table->price_per_hour != $newTable->price_per_hour) {
            return back()->with('error', 'Không thể đổi tự động do đơn giá giờ của hai bàn khác nhau!');
        }

        $session->update(['table_id' => $request->new_table_id]);
        return back()->with('success', 'Chuyển bàn chơi thành công!');
    }

    /**
     * Đồng bộ toàn bộ dịch vụ của phiên (AJAX)
     * Hỗ trợ ghi chú: cùng service_id nhưng ghi chú khác nhau = 2 dòng riêng
     */
    public function syncServices(Request $request) {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'services' => 'present|array',
        ]);

        $session = GameSession::findOrFail($request->session_id);

        // Xóa toàn bộ dịch vụ hiện tại
        DB::table('session_services')->where('session_id', $session->id)->delete();

        // Ghi lại từng dòng (có thể cùng service_id nhưng note khác nhau)
        foreach ($request->services as $srv) {
            $qty = intval($srv['quantity'] ?? 0);
            if ($qty > 0) {
                DB::table('session_services')->insert([
                    'session_id' => $session->id,
                    'service_id' => $srv['id'],
                    'quantity' => $qty,
                    'note' => $srv['note'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // Thanh toán hóa đơn và làm tròn tiền 1.000đ
    public function checkout(Request $request) {
        $request->validate(['session_id' => 'required|exists:sessions,id']);

        $session = GameSession::with('table')->findOrFail($request->session_id);

        // Lấy dịch vụ từ session_services (hỗ trợ nhiều dòng cùng service_id)
        $sessionServices = DB::table('session_services')
            ->join('services', 'services.id', '=', 'session_services.service_id')
            ->where('session_services.session_id', $session->id)
            ->select('services.id', 'services.name', 'services.price', 'session_services.quantity', 'session_services.note')
            ->get();

        $startTime = \Carbon\Carbon::parse($session->start_time);
        $endTime = now();
        $durationSeconds = abs($endTime->timestamp - $startTime->timestamp);
        $durationMinutes = max(1, round($durationSeconds / 60));

        // Tính tiền giờ chơi dựa trên giây để khớp tuyệt đối với frontend
        $diffHours = $durationSeconds / 3600;
        $tableFeeRaw = $diffHours * $session->table->price_per_hour;
        $tableFee = round($tableFeeRaw / 1000) * 1000;

        $servicesFee = 0;
        $itemsData = [];
        foreach ($sessionServices as $service) {
            $subtotal = $service->price * $service->quantity;
            $servicesFee += $subtotal;
            $itemsData[] = [
                'service_id' => $service->id,
                'service_name' => $service->name . ($service->note ? ' (' . $service->note . ')' : ''),
                'price' => $service->price,
                'quantity' => $service->quantity,
                'note' => $service->note,
                'subtotal' => $subtotal
            ];
        }

        $total = $tableFee + $servicesFee;

        $invoiceId = null;
        DB::transaction(function () use ($session, $durationMinutes, $tableFee, $servicesFee, $total, $itemsData, $startTime, $endTime, &$invoiceId) {
            $invoice = Invoice::create([
                'club_id' => $session->club_id,
                'table_id' => $session->table_id,
                'session_id' => $session->id,
                'opened_by' => \Illuminate\Support\Facades\Auth::id(),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $durationMinutes,
                'table_fee' => $tableFee,
                'services_fee' => $servicesFee,
                'total' => $total
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            $session->update(['status' => 'closed']);
            $invoiceId = $invoice->id;
        });

        return response()->json(['success' => true, 'invoice_id' => $invoiceId]);
    }
}
