<?php

namespace App\Http\Controllers\Core;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function store(Request $request)
    {
        Gate::authorize('access', 'contacts.create');

        try {
            $data = $request->validate([
                'type'           => 'required|in:customer,supplier,both',
                'first_name'     => 'nullable|string|max:255',
                'last_name'      => 'nullable|string|max:255',
                'company_name'   => 'nullable|string|max:255',
                'national_code'  => 'nullable|string|max:20',
                'economic_code'  => 'nullable|string|max:20',
                'mobile'         => 'nullable|string|max:20',
                'phone'          => 'nullable|string|max:20',
                'email'          => 'nullable|email|max:255',
                'website'        => 'nullable|string|max:255',
                'address'        => 'nullable|string',
                'description'    => 'nullable|string',
                'is_active'      => 'boolean',
            ]);

            $data['tenant_id'] = $this->manager->getTenantId();

            Contact::create($data);

            return redirect()->route('core.contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد مخاطب'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد مخاطب: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, Contact $contact)
    {
        Gate::authorize('access', 'contacts.edit');

        try {
            $contact->update($request->validate([
                'type'           => 'required|in:customer,supplier,both',
                'first_name'     => 'nullable|string|max:255',
                'last_name'      => 'nullable|string|max:255',
                'company_name'   => 'nullable|string|max:255',
                'national_code'  => 'nullable|string|max:20',
                'economic_code'  => 'nullable|string|max:20',
                'mobile'         => 'nullable|string|max:20',
                'phone'          => 'nullable|string|max:20',
                'email'          => 'nullable|email|max:255',
                'website'        => 'nullable|string|max:255',
                'address'        => 'nullable|string',
                'description'    => 'nullable|string',
                'is_active'      => 'boolean',
            ]));

            return redirect()->route('core.contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش مخاطب'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش مخاطب: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(Contact $contact)
    {
        Gate::authorize('access', 'contacts.delete');

        try {
            $contact->delete();

            return redirect()->route('core.contacts.index')->with('toast', [
                'message' => 'مخاطب با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف مخاطب'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف مخاطب: ' . $e->getMessage()]);
        }
    }
}