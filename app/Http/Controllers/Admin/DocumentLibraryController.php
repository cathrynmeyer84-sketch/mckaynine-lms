<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentLibraryController extends Controller
{
    public function index()
    {
        $documents = InfoDocument::latest()->get();
        return view('admin.documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt,csv,ppt,pptx|max:20480',
        ]);

        foreach ($request->file('files') as $file) {
            $path = $file->store('documents', 'public');
            InfoDocument::create([
                'name'      => $file->getClientOriginalName(),
                'path'      => $path,
                'mime_type' => $file->getMimeType(),
                'size'      => $file->getSize(),
            ]);
        }

        return back()->with('success', count($request->file('files')) . ' document(s) uploaded.');
    }

    public function destroy(InfoDocument $document)
    {
        Storage::disk('public')->delete($document->path);
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
