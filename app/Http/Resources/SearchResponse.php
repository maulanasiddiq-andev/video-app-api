<?php

namespace App\Http\Resources;

class SearchResponse
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public mixed $items;
    public int $total_item;
    public int $current_page;
    public int $page_size;
    public int $total_pages;
    public bool $has_next_page;
    public bool $has_previous_page;

    public function __construct(mixed $collection)
    {
        $meta = $collection["meta"];

        $this->items = $collection["data"];
        $this->total_item = $meta["total"];
        $this->current_page = $meta["current_page"];
        $this->page_size = $meta["per_page"];
        $this->total_pages = $meta["last_page"];
        $this->has_next_page = $this->current_page < $this->total_pages;
        $this->has_previous_page = $this->current_page > 1;
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'totalItem' => $this->total_item,
            'currentPage' => $this->current_page,
            'pageSize' => $this->page_size,
            'totalPages' => $this->total_pages,
            'hasNextPage' => $this->has_next_page,
            'hasPreviousPage' => $this->has_previous_page,
        ];
    }
}
