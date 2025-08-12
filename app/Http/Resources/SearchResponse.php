<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
// use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\JsonResource;

class SearchResponse
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public array $items;
    public int $total_item;
    public int $current_page;
    public int $page_size;
    public int $total_pages;
    public bool $has_next_page;
    public bool $has_previous_page;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->items = $paginator->items();
        $this->total_item = $paginator->total();
        $this->current_page = $paginator->currentPage();
        $this->page_size = $paginator->perPage();
        $this->total_pages = $paginator->lastPage();
        $this->has_next_page = $paginator->currentPage() < $paginator->lastPage();
        $this->has_previous_page = $paginator->currentPage() > 1;
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total_item' => $this->total_item,
            'current_page' => $this->current_page,
            'page_size' => $this->page_size,
            'total_pages' => $this->total_pages,
            'has_next_page' => $this->has_next_page,
            'has_previous_page' => $this->has_previous_page,
        ];
    }
}
