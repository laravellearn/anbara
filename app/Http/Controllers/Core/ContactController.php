<?php

namespace App\Http\Controllers\Core;

use App\Models\Contact;
use App\Models\Country;
use App\Models\Province;
use App\Http\Requests\Core\StoreContactRequest;
use App\Http\Requests\Core\UpdateContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ContactController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'contacts.view');
        $tenantId = $this->manager->getTenantId();

        $query = Contact::where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $contacts = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Contact::where('tenant_id', $tenantId)->count(),
            'customer' => Contact::where('tenant_id', $tenantId)->where('type', 'customer')->count(),
            'supplier' => Contact::where('tenant_id', $tenantId)->where('type', 'supplier')->count(),
            'both'     => Contact::where('tenant_id', $tenantId)->where('type', 'both')->count(),
            'active'   => Contact::where('tenant_id', $tenantId)->where('is_active', true)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('core.contacts._table', compact('contacts'))->render(),
                'statsHtml' => view('core.contacts._stats', compact('stats'))->render(),
                'total'     => $contacts->total(),
            ]);
        }

        return view('core.contacts.index', compact('contacts', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'contacts.create');
        $countries = Country::orderBy('name')->get();
        return view('core.contacts.create', compact('countries'));
    }

    public function store(StoreContactRequest $request)
    {
        Gate::authorize('access', 'contacts.create');

        try {
            $tenantId = $this->manager->getTenantId();
            $data = $request->validated();

            $data['tenant_id'] = $tenantId;
            if (empty($data['code'])) {
                $data['code'] = $this->generateNextCode($tenantId);
            }

            Contact::create($data);

            return redirect()->route('contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد مخاطب'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطا در ایجاد مخاطب'])->withInput();
        }
    }

    // صفحه ویرایش (جدید)
    public function edit(Contact $contact)
    {
        Gate::authorize('access', 'contacts.edit');
        $countries = Country::orderBy('name')->get();
        // استان‌های مرتبط با کشور مخاطب را بگیر
        $provinces = Province::where('country_id', $contact->country_id)->orderBy('name')->get();
        return view('core.contacts.edit', compact('contact', 'countries', 'provinces'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        Gate::authorize('access', 'contacts.edit');

        try {
            $data = $request->validated();

            $contact->update($data);

            return redirect()->route('contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش مخاطب'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطا در ویرایش مخاطب'])->withInput();
        }
    }

    public function destroy(Contact $contact)
    {
        Gate::authorize('access', 'contacts.delete');

        try {
            $contact->delete();

            return redirect()->route('contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف مخاطب'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف مخاطب']);
        }
    }

    /**
     * تولید خودکار کد بعدی طرف‌حساب در قالب C-00001، C-00002 و ...
     * بر اساس بیشترین عدد موجود در tenant جاری (حتی رکوردهای soft-delete شده
     * را هم در نظر می‌گیرد تا کد تکراری دوباره صادر نشود).
     */
    protected function generateNextCode(int $tenantId): string
    {
        $lastNumber = Contact::withTrashed()
            ->where('tenant_id', $tenantId)
            ->where('code', 'like', 'C-%')
            ->get()
            ->map(fn($contact) => (int) str_replace('C-', '', $contact->code))
            ->max();

        return 'C-' . str_pad((string) (($lastNumber ?? 0) + 1), 5, '0', STR_PAD_LEFT);
    }
}
