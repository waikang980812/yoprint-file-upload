<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Jobs\ProcessCsvUpload;
use App\Http\Resources\UploadResource;

class UploadController extends Controller
{
    public function list()
    {
        $uploads = Upload::orderByDesc('id')->get();
        return response()->json([
            'data' => UploadResource::collection($uploads)
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:51200',
        ]);

        $path = $request->file('file')->store('uploads');
        $filename = $request->file('file')->getClientOriginalName();

        $upload = Upload::create([
            'file_path' => $path,
            'status' => 'pending',
            'file_name' => $filename,
            'uploaded_at' => now(),
        ]);

        ProcessCsvUpload::dispatch($upload);

        return response()->json([
            'message' => 'File uploaded and processing started.',
            'upload' => new UploadResource($upload)
        ]);
    }

    public function index()
    {
        try {
            return view('uploads.index');
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function show($id)
    {
        $upload = Upload::findOrFail($id);
        return new UploadResource($upload);
    }
}
