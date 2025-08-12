<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 10);

        $histories = History::filter($request)
                    ->where('user_id', $request->user()->id)
                    ->paginate($page_size);

        $response = [
            "succeed" => true,
            "messages" => [],
            "data" => [
                'items' => $histories->items(),
                'total_item' => $histories->total(),
                'current_page' => $histories->currentPage(),
                'page_size' => $histories->perPage(),
                'total_pages' => $histories->lastPage(),
                'has_previous_page' => $histories->currentPage() > 1,
                'has_next_page' => $histories->currentPage() < $histories->lastPage()
            ]
        ];

        return response()->json($response);
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

        $response = [
            'succeed' => true,
            'messages' => [],
            'data' => null
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(History $history)
    {
        //
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
        $validated = $request->validated();

        $history->update($validated);

        $response = [
            'succeed' => true,
            'messages' => [],
            'data' => null
        ];

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(History $history)
    {
        //
    }
}
