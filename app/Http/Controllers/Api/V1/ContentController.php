<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Http\Resources\Api\V1\ContentCollection;
use App\Http\Resources\Api\V1\ContentResource;
use Validator;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->query('per_page', 10);
        $search = $request->query('search', '');

        $query = Content::query();

        if (!empty($search)) {
            $query->where('title', 'like', "%$search%")
                  ->orWhere('body', 'like', "%$search%");
        }

        $contents = $query->paginate($per_page);

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully retrieved',
            'data' => new ContentCollection($contents),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ], 422);
        }

        $content = Content::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully stored',
            'data' => new ContentResource($content),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $content = Content::find($id);
        if (!$content) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully retrieved',
            'data' => new ContentResource($content),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ], 422);
        }

        $content = Content::find($id);
        if (!$content) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $content->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully updated',
            'data' => new ContentResource($content),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $content = Content::find($id);
        if (!$content) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $content->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data successfully deleted',
        ], 200);
    }
}
