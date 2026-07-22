<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * مدیریت پیوست‌ها برای اسناد انبار و سفارشات خرید
 */
class AttachmentController extends BaseController
{
    // انواع مجاز
    private const ALLOWED_TYPES = ['pdf','jpg','jpeg','png','gif','doc','docx','xls','xlsx','zip'];
    private const MAX_SIZE_KB    = 10240; // 10MB

    /** آپلود پیوست جدید */
    public function store(Request $request)
    {
        Gate::authorize('access', 'attachments.create');

        $request->validate([
            'attachable_type' => ['required', 'in:WarehouseDocument,PurchaseOrder'],
            'attachable_id'   => ['required', 'integer'],
            'file'            => ['required', 'file', 'max:' . self::MAX_SIZE_KB,
                'mimes:' . implode(',', self::ALLOWED_TYPES)],
            'description'     => ['nullable', 'string', 'max:255'],
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        // بررسی تعلق سند به tenant
        $this->verifyOwnership($request->attachable_type, $request->attachable_id, $tenantId, $companyId);

        $file     = $request->file('file');
        $origName = $file->getClientOriginalName();
        $ext      = strtolower($file->getClientOriginalExtension());
        $path     = "attachments/{$tenantId}/{$companyId}/" . Str::uuid() . ".{$ext}";

        Storage::disk('local')->put($path, file_get_contents($file));

        Attachment::create([
            'tenant_id'       => $tenantId,
            'company_id'      => $companyId,
            'attachable_type' => 'App\\Models\\' . $request->attachable_type,
            'attachable_id'   => $request->attachable_id,
            'file_name'       => $origName,
            'file_path'       => $path,
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
            'description'     => $request->description,
            'uploaded_by'     => auth()->id(),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'فایل پیوست با موفقیت آپلود شد.']);
    }

    /** دانلود پیوست */
    public function download(Attachment $attachment)
    {
        Gate::authorize('access', 'attachments.view');
        if ($attachment->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'فایل یافت نشد.');
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
    }

    /** حذف پیوست */
    public function destroy(Attachment $attachment)
    {
        Gate::authorize('access', 'attachments.delete');
        if ($attachment->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        Storage::disk('local')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'پیوست حذف شد.']);
    }

    // ─── helper ───────────────────────────────────────────────────────────────
    private function verifyOwnership(string $type, int $id, int $tenantId, int $companyId): void
    {
        $modelClass = 'App\\Models\\' . $type;
        $record = $modelClass::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->firstOrFail();
    }
}
