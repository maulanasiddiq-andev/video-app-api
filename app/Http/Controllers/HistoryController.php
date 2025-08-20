<?php

namespace App\Http\Controllers;

use App\Enums\RecordStatusConstant;
use App\Models\History;
use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\HistoryResource;
use App\Http\Resources\SearchResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('pageSize', 10);

        $histories = History::record($request)
                    ->filter($request)
                    ->with('video.user')
                    ->where('user_id', $request->user()->id)
                    ->paginate($page_size);

        $collection = HistoryResource::collection($histories)->response()->getData(true);
        $search_response = new SearchResponse($collection);
        $base_response = new BaseResponse(true, [], $search_response->toArray());

        return response()->json($base_response->toArray());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHistoryRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        History::create($validated);

        $base_response = new BaseResponse(true, ['Riwayat berhasil ditambahkan'], null);

        return response()->json($base_response->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(History $history)
    {
        if ($history->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }  

        $existing_history = $history->load(['user', 'video.user']);

        $resource = $existing_history->toResource();
        $base_response = new BaseResponse(true, [], $resource);

        return response()->json($base_response->toArray());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHistoryRequest $request, History $history)
    {
        if ($history->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }  

        $validated = $request->validated();
        $history->update($validated);

        $base_response = new BaseResponse(true, ['Komentar berhasil diupdate'], $history->load('user'));

        return response()->json($base_response->toArray());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(History $history)
    {
        if ($history->record_status == RecordStatusConstant::deleted) {
            throw new NotFoundHttpException();
        }

        $history->record_status = RecordStatusConstant::deleted;
        $history->save();
        $base_response = new BaseResponse(true, ['Riwayat berhasil dihapus'], null);

        return response()->json($base_response->toArray());
    }
}
