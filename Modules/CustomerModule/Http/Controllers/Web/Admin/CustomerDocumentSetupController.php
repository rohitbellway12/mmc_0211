<?php

namespace Modules\CustomerModule\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\CustomerModule\Entities\CustomerDocumentType;

class CustomerDocumentSetupController extends Controller
{
    public function index(Request $request): Renderable
    {
        $search = $request->get('search', '');
        $documentTypes = CustomerDocumentType::when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(pagination_limit());

        return view('customermodule::admin.document-setup.index', compact('documentTypes', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:191|unique:customer_document_types,title',
            'description' => 'nullable|string|max:500',
            'is_required' => 'nullable|boolean',
        ]);

        CustomerDocumentType::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_required' => $request->has('is_required') ? 1 : 0,
            'is_active' => 1,
        ]);

        Toastr::success(translate('Document created successfully'));
        return back();
    }

    public function edit($id): Renderable
    {
        $documentType = CustomerDocumentType::findOrFail($id);
        return view('customermodule::admin.document-setup.edit', compact('documentType'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:191|unique:customer_document_types,title,' . $id,
            'description' => 'nullable|string|max:500',
            'is_required' => 'nullable|boolean',
        ]);

        $documentType = CustomerDocumentType::findOrFail($id);
        $documentType->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_required' => $request->has('is_required') ? 1 : 0,
        ]);

        Toastr::success(translate('Document updated successfully'));
        return redirect()->route('admin.customer.document-setup.index');
    }

    public function statusUpdate($id): RedirectResponse
    {
        $documentType = CustomerDocumentType::findOrFail($id);
        $documentType->is_active = !$documentType->is_active;
        $documentType->save();

        Toastr::success(translate('Status updated successfully'));
        return back();
    }

    public function destroy($id): RedirectResponse
    {
        $documentType = CustomerDocumentType::findOrFail($id);
        $documentType->delete();

        Toastr::success(translate('Document deleted successfully'));
        return back();
    }
}

