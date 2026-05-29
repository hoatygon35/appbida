<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller {
    public function index() {
        $clubId = auth()->user()->club_id;
        $debts = Debt::where('club_id', $clubId)->where('total_debt', '>', 0)->orderBy('total_debt', 'desc')->get();
        $totalDebt = $debts->sum('total_debt');
        return view('admin.debts.index', compact('debts', 'totalDebt'));
    }

    public function store(Request $request) {
        $request->validate([
            'customer_name' => 'required|string',
            'amount' => 'required|numeric|min:1000',
            'note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $debt = Debt::firstOrCreate(
                ['customer_name' => trim($request->customer_name), 'club_id' => auth()->user()->club_id]
            );
            $debt->transactions()->create([
                'amount' => $request->amount,
                'note' => $request->note
            ]);
            $debt->increment('total_debt', $request->amount);
        });

        return back()->with('success', 'Đã ghi nhận khoản nợ mới!');
    }

    public function pay(Request $request) {
        $request->validate([
            'debt_id' => 'required|exists:debts,id',
            'pay_amount' => 'required|numeric|min:1000',
            'admin_password' => 'required'
        ]);

        // Xác thực bảo mật: Đóng vai trò kiểm tra bảo mật bằng mật khẩu Admin
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'Mật khẩu quản trị viên không chính xác!');
        }

        $debt = Debt::where('club_id', auth()->user()->club_id)->findOrFail($request->debt_id);

        if ($request->pay_amount > $debt->total_debt) {
            return back()->with('error', 'Số tiền thanh toán vượt quá số nợ hiện có!');
        }

        DB::transaction(function () use ($debt, $request) {
            $debt->transactions()->create([
                'amount' => -$request->pay_amount,
                'note' => 'Trả nợ'
            ]);
            $debt->decrement('total_debt', $request->pay_amount);
        });

        return back()->with('success', 'Xác nhận trả nợ thành công!');
    }
}
