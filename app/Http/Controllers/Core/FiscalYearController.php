<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Services\FiscalYearService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FiscalYearController extends Controller
{

    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;

        // اگر کاربر مالک نباشد دسترسی ندارد (مطابق Middleware)
        if (!auth()->user()->isOwner()) {
            abort(403);
        }

        $query = $tenant->fiscalYears()->latest();

        // فیلتر بر اساس وضعیت
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'closed') {
                $query->where('is_closed', true);
            }
        }

        // مرتب‌سازی
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'start_date', 'end_date', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $perPage = $request->input('per_page', 20);
        $fiscalYears = $query->paginate($perPage)->appends($request->query());

        // اگر درخواست AJAX بود، فقط جدول را برگردان
        if ($request->ajax()) {
            $html = view('fiscal-years._table', compact('fiscalYears'))->render();
            return response()->json([
                'html'  => $html,
                'total' => $fiscalYears->total(),
            ]);
        }

        // داده‌های آماری برای کارت‌ها
        $stats = [
            'total'   => $tenant->fiscalYears()->count(),
            'active'  => $tenant->fiscalYears()->where('is_active', true)->count(),
            'closed'  => $tenant->fiscalYears()->where('is_closed', true)->count(),
            'current' => $tenant->activeFiscalYear?->name ?? '---',
        ];

        return view('core.fiscal-years.index', compact('fiscalYears', 'stats'));
    }

    public function create()
    {
        return view('fiscal-years.create');
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'sometimes|boolean',
        ]);

        // اطمینان از عدم تداخل تاریخ با سال‌های دیگر
        $overlap = $tenant->fiscalYears()
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'بازهٔ تاریخ با سال مالی دیگری تداخل دارد.'])->withInput();
        }

        $data['tenant_id'] = $tenant->id;

        $fiscalYear = FiscalYear::create($data);

        // اگر فعال بود، بقیه را غیرفعال کند
        if ($fiscalYear->is_active) {
            $fiscalYear->activate();
        }

        return redirect()->route('core.fiscal-years.index')->with('success', 'سال مالی جدید ایجاد شد.');
    }

    public function edit(FiscalYear $fiscalYear)
    {
        $this->authorize('update', $fiscalYear); // Policy (اختیاری)
        return view('core.fiscal-years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $this->authorize('update', $fiscalYear);
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'sometimes|boolean',
        ]);

        // بررسی تداخل (به جز خودش)
        $overlap = $tenant->fiscalYears()
            ->where('id', '!=', $fiscalYear->id)
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('start_date', '<=', $data['start_date'])
                            ->where('end_date', '>=', $data['end_date']);
                    });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'بازهٔ تاریخ با سال مالی دیگری تداخل دارد.'])->withInput();
        }

        $fiscalYear->update($data);

        if ($fiscalYear->is_active) {
            $fiscalYear->activate();
        }

        return redirect()->route('fiscal-years.index')->with('success', 'سال مالی ویرایش شد.');
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $this->authorize('delete', $fiscalYear);
        $fiscalYear->delete();
        return redirect()->route('fiscal-years.index')->with('success', 'سال مالی حذف شد.');
    }

    // فعال‌سازی یک سال خاص
    public function activate(FiscalYear $fiscalYear)
    {
        $fiscalYear->activate();
        return back()->with('success', 'سال مالی فعال شد.');
    }

    // بستن سال مالی
    public function close(FiscalYear $fiscalYear)
    {
        $fiscalYear->close();
        return back()->with('success', 'سال مالی بسته شد.');
    }
}
