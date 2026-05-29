<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Table;
use App\Models\User;
use App\Models\GameSession;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $clubId = $user->club_id;
        
        // Force user to only see invoices tab
        if ($user->role === 'user' && $request->input('tab', 'invoices') !== 'invoices') {
            return redirect()->route('admin.dashboard', ['tab' => 'invoices']);
        }

        // Tự động xóa các hóa đơn lưu giữ quá 10 ngày trong DB
        Invoice::where('club_id', $clubId)
            ->where('created_at', '<', now()->subDays(10))
            ->delete();

        // 1. Lấy danh sách nhân viên của chi nhánh
        $staff = User::where('club_id', $clubId)
            ->where('role', 'user')
            ->get();

        // 2. Lấy danh sách bàn chơi của chi nhánh
        $tables = Table::where('club_id', $clubId)->get();

        // 3. Lấy danh sách dịch vụ của chi nhánh
        $services = Service::where('club_id', $clubId)->get();

        // 4. Lọc hóa đơn theo bàn và theo ngày
        $selectedTableId = $request->input('table_id');
        $selectedDate = $request->input('date');

        $invoicesQuery = Invoice::with(['table', 'employee', 'items'])
            ->where('club_id', $clubId);

        if ($user->role === 'user') {
            $invoicesQuery->where('opened_by', $user->id);
        }

        if ($selectedTableId) {
            $invoicesQuery->where('table_id', $selectedTableId);
        }

        if ($selectedDate) {
            $invoicesQuery->whereDate('created_at', $selectedDate);
        } else {
            // Nếu không chọn ngày, mặc định lấy trong ngày hôm nay để tránh load quá nhiều
            $invoicesQuery->whereDate('created_at', today());
        }

        $invoices = $invoicesQuery->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        // 5. Tính toán các số liệu Dashboard
        $activeSessions = GameSession::with(['table', 'employee'])
            ->where('club_id', $clubId)
            ->where('status', 'open')
            ->get();
            
        $activeTablesCount = $activeSessions->count();
        $emptyTablesCount = max(0, $tables->count() - $activeTablesCount);
        
        $invoicesTodayCount = Invoice::where('club_id', $clubId)
            ->whereDate('created_at', today())
            ->count();
            
        $revenueToday = Invoice::where('club_id', $clubId)
            ->whereDate('created_at', today())
            ->sum('total');

        $todayInvoices = Invoice::with(['table', 'employee', 'items'])
            ->where('club_id', $clubId)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // 6. Lấy danh sách ghi chú
        $notes = Note::with('creator')
            ->where('club_id', $clubId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'staff', 
            'tables', 
            'services', 
            'invoices', 
            'selectedTableId', 
            'selectedDate',
            'activeSessions',
            'activeTablesCount',
            'emptyTablesCount',
            'invoicesTodayCount',
            'revenueToday',
            'todayInvoices',
            'notes'
        ));
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'club_id' => auth()->user()->club_id,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'staff'])->with('success', 'Thêm nhân viên mới thành công!');
    }
    public function updateStaff(Request $request, $id)
    {
        $employee = User::where('id', $id)->where('club_id', auth()->user()->club_id)->firstOrFail();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $employee->name = $request->name;
        $employee->username = $request->username;
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }
        $employee->save();

        return redirect()->route('admin.dashboard', ['tab' => 'staff'])->with('success', 'Cập nhật nhân viên thành công!');
    }

    public function destroyStaff($id)
    {
        $employee = User::where('id', $id)->where('club_id', auth()->user()->club_id)->firstOrFail();
        $employee->delete();
        return redirect()->route('admin.dashboard', ['tab' => 'staff'])->with('success', 'Đã xóa nhân viên!');
    }

    public function storeTable(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
        ]);

        $price = $request->has('is_cafe') ? 0 : ($request->input('price_per_hour') ?? 0);

        Table::create([
            'name' => $request->name,
            'price_per_hour' => $price,
            'club_id' => auth()->user()->club_id,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'tables'])->with('success', 'Thêm bàn chơi mới thành công!');
    }

    public function storeService(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:thức uống,đồ ăn,khác'],
        ]);

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'club_id' => auth()->user()->club_id,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'services'])->with('success', 'Thêm dịch vụ mới thành công!');
    }

    public function updateSettings(Request $request)
    {
        $club = auth()->user()->club;

        if ($request->has('delete_qr')) {
            $club->update(['qr_code' => null]);
            return redirect()->back()->with('status', 'qr-deleted')->with('success', 'Đã xóa mã QR thanh toán thành công!');
        }

        $request->validate([
            'qr_code' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getPathname()));
            $club->update(['qr_code' => $base64]);
        }

        return redirect()->back()->with('status', 'qr-updated')->with('success', 'Cập nhật mã QR thanh toán thành công!');
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        Note::create([
            'club_id' => auth()->user()->club_id,
            'title' => $request->title,
            'content' => $request->content,
            'created_by' => auth()->user()->id,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'notes'])->with('success', 'Thêm ghi chú mới thành công!');
    }

    public function destroyNote(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:notes,id'],
        ]);

        $note = Note::where('club_id', auth()->user()->club_id)->findOrFail($request->id);
        $note->delete();

        return redirect()->route('admin.dashboard', ['tab' => 'notes'])->with('success', 'Xóa ghi chú thành công!');
    }

    public function updateTable(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:tables,id'],
            'name' => ['required', 'string', 'max:255'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
        ]);

        $table = Table::where('club_id', auth()->user()->club_id)->findOrFail($request->id);

        $price = $request->has('is_cafe') ? 0 : ($request->input('price_per_hour') ?? 0);

        $table->update([
            'name' => $request->name,
            'price_per_hour' => $price,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'tables'])->with('success', 'Cập nhật thông tin bàn chơi thành công!');
    }

    public function destroyTable(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:tables,id'],
        ]);

        $table = Table::where('club_id', auth()->user()->club_id)->findOrFail($request->id);
        $table->delete();

        return redirect()->route('admin.dashboard', ['tab' => 'tables'])->with('success', 'Xóa bàn chơi thành công!');
    }

    public function updateService(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:services,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:thức uống,đồ ăn,khác'],
        ]);

        $service = Service::where('club_id', auth()->user()->club_id)->findOrFail($request->id);
        $service->update([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'services'])->with('success', 'Cập nhật thông tin dịch vụ thành công!');
    }

    public function destroyService(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:services,id'],
        ]);

        $service = Service::where('club_id', auth()->user()->club_id)->findOrFail($request->id);
        $service->delete();

        return redirect()->route('admin.dashboard', ['tab' => 'services'])->with('success', 'Xóa dịch vụ thành công!');
    }
}
