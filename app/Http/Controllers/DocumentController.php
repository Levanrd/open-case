<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __invoke(Request $request, Document $document): StreamedResponse
    {
        $case = $document->taxCase()->firstOrFail();
        $user = $request->user();

        $allowed = $user->isAdmin()
            || $case->user_id === $user->id
            || $case->assigned_accountant_id === $user->id;

        abort_unless($allowed, 403);
        abort_unless($document->file_path && Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }
}
