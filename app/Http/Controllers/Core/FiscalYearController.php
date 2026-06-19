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

        if (!auth()->user()->isOwner()) {
            abort(403);
        }

        $query = $tenant->fiscalYears()->latest();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'closed') {
                $query->where('is_closed', true);
            }
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
            'total'   => $tenant->fiscalYears()->count(),
            'active'  => $tenant->fiscalYears()->where('is_active', true)->count(),
            'closed'  => $tenant->fiscalYears()->where('is_closed', true)->count(),
            'current' => $tenant->activeFiscalYear?->name ?? '---',
        ];

        return view('core.fiscal-years.index', compact('fiscalYears', 'stats'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        try {
            $data = $request->validate([
                'name'       => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
                'is_active'  => 'sometimes|boolean',
            ]);

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
                return redirect()->back()
                    ->withErrors(['start_date' => 'بازهٔ تاریخ با سال مالی دیگری تداخل دارد.'])
                    ->withInput()
                    ->with('show_create_modal', true);
            }

            $data['tenant_id'] = $tenant->id;

            $fiscalYear = FiscalYear::create($data);

            if ($fiscalYear->is_active) {
                $fiscalYear->activate();
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

    public function edit(FiscalYear $fiscalYear)
    {
        $this->authorize('update', $fiscalYear);
        return view('core.fiscal-years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $this->authorize('update', $fiscalYear);
        $tenant = auth()->user()->tenant;

        try {
            $data = $request->validate([
                'name'       => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date'   => 'required|date|after:start_date',
                'is_active'  => 'sometimes|boolean',
            ]);

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
        $this->authorize('delete', $fiscalYear);

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
        try {
            $fiscalYear->activate();

            return redirect()->back()->with('toast', [
                'message' => 'سال مالی با موفقیت فعال شد.',
                'type'    => 'success',
                'title'   => 'فعال‌سازی سال مالی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در فعال‌سازی سال مالی: ' . $e->getMessage()]);
        }
    }

    public function close(FiscalYear $fiscalYear)
    {
        try {
            $fiscalYear->close();

            return redirect()->back()->with('toast', [
                'message' => 'سال مالی با موفقیت بسته شد.',
                'type'    => 'success',
                'title'   => 'بستن سال مالی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در بستن سال مالی: ' . $e->getMessage()]);
        }
    }
}