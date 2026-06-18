<?php

namespace App\Http\Controllers\Core;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContactController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'contacts.view');

        $contacts = Contact::where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        return view('core.contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'contacts.create');

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

        flash()->success('مخاطب ایجاد شد.');
        return redirect()->route('core.contacts.index');
    }

    public function update(Request $request, Contact $contact)
    {
        Gate::authorize('access', 'contacts.edit');

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

        flash()->success('مخاطب ویرایش شد.');
        return redirect()->route('core.contacts.index');
    }

    public function destroy(Contact $contact)
    {
        Gate::authorize('access', 'contacts.delete');

        $contact->delete();

        flash()->success('مخاطب حذف شد.');
        return back();
    }
}