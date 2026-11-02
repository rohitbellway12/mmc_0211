<?php

namespace Modules\CustomerModule\Http\Controllers\Api\V1\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\CustomerModule\Entities\CustomerDocument;
use Modules\CustomerModule\Entities\CustomerDocumentType;

class CustomerDocumentController extends Controller
{
    /**
     * Display a listing of required documents with customer's upload status.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $documentTypes = CustomerDocumentType::where('is_active', 1)
            ->latest()
            ->get()
            ->map(function ($docType) use ($user) {
                $userDoc = CustomerDocument::where('user_id', $user->id)
                    ->where('document_type_id', $docType->id)
                    ->latest()
                    ->first();

                return [
                    'id' => $docType->id,
                    'title' => $docType->title,
                    'description' => $docType->description,
                    'is_required' => (bool)$docType->is_required,
                    'is_uploaded' => $userDoc ? true : false,
                    'uploaded_document' => $userDoc ? [
                        'id' => $userDoc->id,
                        'file_path' => $userDoc->file_path,
                        'file_path_full_path' => $userDoc->file_path_full_path,
                        'file_name' => $userDoc->file_name,
                        'uploaded_at' => $userDoc->created_at,
                    ] : null,
                ];
            });

        return response()->json(response_formatter(DEFAULT_200, $documentTypes), 200);
    }

    /**
     * Get all documents uploaded by this customer.
     * @param Request $request
     * @return JsonResponse
     */
    public function myDocuments(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $documents = CustomerDocument::with('documentType')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json(response_formatter(DEFAULT_200, $documents), 200);
    }

    /**
     * Upload or update a customer document.
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $validator = Validator::make($request->all(), [
            'document_type_id' => 'required|exists:customer_document_types,id',
            'file' => 'required|file|mimes:jpeg,jpg,png,pdf,webp,doc,docx|max:15000',
        ], [
            'document_type_id.required' => translate('Please select a document type.'),
            'document_type_id.exists' => translate('Invalid document type selected.'),
            'file.required' => translate('Please select a file to upload.'),
            'file.mimes' => translate('Supported formats are: JPEG, JPG, PNG, WEBP, PDF, DOC, DOCX.'),
        ]);

        if ($validator->fails()) {
            return response()->json(response_formatter(DEFAULT_400, null, error_processor($validator)), 400);
        }

        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedName = file_uploader('customer/document/', $extension, $uploadedFile);

        $customerDoc = CustomerDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'document_type_id' => $request->document_type_id,
            ],
            [
                'file_path' => $storedName,
                'file_name' => $uploadedFile->getClientOriginalName(),
            ]
        );

        return response()->json(response_formatter(DEFAULT_STORE_200, [
            'id' => $customerDoc->id,
            'document_type_id' => $customerDoc->document_type_id,
            'file_name' => $customerDoc->file_name,
            'file_path' => $customerDoc->file_path,
            'file_path_full_path' => $customerDoc->file_path_full_path,
            'created_at' => $customerDoc->created_at,
        ]), 200);
    }

    /**
     * Delete an uploaded document.
     * @param Request $request
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->user_type, CUSTOMER_USER_TYPES)) {
            return response()->json(response_formatter(DEFAULT_403), 401);
        }

        $doc = CustomerDocument::where('user_id', $user->id)->find($id);
        if (!$doc) {
            return response()->json(response_formatter(DEFAULT_404), 404);
        }

        $doc->delete();

        return response()->json(response_formatter(DEFAULT_DELETE_200), 200);
    }
}

