<?php

namespace App\Http\Controllers\Core;

use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class FiscalYearController extends BaseController
{
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $companyId = $this->manager->getCompanyId();

        $query = $tenant->fiscalYears()->where('company_id', $companyId)->latest();

        // فیلتر وضعیت
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'closed') {
                $query->where('is_closed', true);
            }
        }

        // جستجو
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'start_date', 'end_date', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $perPage = $request->input('per_page', 20);
        $fiscalYears = $query->paginate($perPage)->appends($request->query());

        if ($request->ajax()) {
            $html = view('core.fiscal-years._table', compact('fiscalYears'))->render();
            return response()->json([
                'html'  => $html,
                'total' => $fiscalYears->total(),
            ]);
        }

        $stats = [
            'total'   => $tenant->fiscalYears()->where('company_id', $companyId)->count(),
            'active'  => $tenant->fiscalYears()->where('company_id', $companyId)->where('is_active', true)->count(),
            'closed'  => $tenant->fiscalYears()->where('company_id', $companyId)->where('is_closed', true)->count(),
            'current' => $tenant->fiscalYears()->where('company_id', $companyId)->where('is_active', true)->first()?->name ?? '---',
        ];

        return view('core.fiscal-years.index', compact('fiscalYears', 'stats'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name'       => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
                'is_active'  => 'sometimes|boolean',
            ]);

            $data['tenant_id']  = auth()->user()->tenant_id;
            $data['company_id'] = $this->manager->getCompanyId();

            // بررسی تداخل تاریخ با سال‌های همین سازمان
            $overlap = FiscalYear::where('company_id', $data['company_id'])
                ->where(function ($q) use ($data) {
                    $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                        ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                        ->orWhere(function ($q) use ($data) {
                            $q->where('start_date', '<=', $data['start_date'])
                                ->where('end_date', '>=', $data['end_date']);
                        });
                })->exists();

            if ($overlap) {
                return redirect()->back()
                    ->withErrors(['start_date' => 'بازهٔ تاریخ با سال مالی دیگری تداخل دارد.'])
                    ->withInput()
                    ->with('show_create_modal', true);
            }

            $fiscalYear = FiscalYear::create($data);

            if ($fiscalYear->is_active) {
                $fiscalYear->activate(); // فقط سال‌های همین سازمان را غیرفعال می‌کند
            }

            return redirect()->route('core.fiscal-years.index')->with('toast', [
                'message' => 'سال مالی جدید با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد سال مالی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد سال مالی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        try {
            $data = $request->validate([
                'name'       => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
                'is_active'  => 'sometimes|boolean',
            ]);

            // بررسی تداخل (به جز خودش)
            $overlap = FiscalYear::where('company_id', $fiscalYear->company_id)
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
                return redirect()->back()
                    ->withErrors(['start_date' => 'بازهٔ تاریخ با سال مالی دیگری تداخل دارد.'])
                    ->withInput()
                    ->with('show_edit_modal', true);
            }

            $fiscalYear->update($data);

            if ($fiscalYear->is_active) {
                $fiscalYear->activate();
            }

            return redirect()->route('core.fiscal-years.index')->with('toast', [
                'message' => 'سال مالی با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش سال مالی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش سال مالی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        try {
            $fiscalYear->delete();

            return redirect()->route('core.fiscal-years.index')->with('toast', [
                'message' => 'سال مالی با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف سال مالی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف سال مالی: ' . $e->getMessage()]);
        }
    }

    public function activate(FiscalYear $fiscalYear)
    {
        $fiscalYear->activate();
        return redirect()->back()->with('toast', [
            'message' => 'سال مالی فعال شد.',
            'type'    => 'success',
            'title'   => 'فعال‌سازی سال مالی'
        ]);
    }

    public function close(FiscalYear $fiscalYear)
    {
        $fiscalYear->close();
        return redirect()->back()->with('toast', [
            'message' => 'سال مالی بسته شد.',
            'type'    => 'success',
            'title'   => 'بستن سال مالی'
        ]);
    }
}