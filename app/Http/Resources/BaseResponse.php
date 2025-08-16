<?php

namespace App\Http\Resources;

// use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\JsonResource;

class BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public bool $succeed;
    public array $messages;
    public mixed $data;

    public function __construct($succeed, $messages, $data)
    {
        $this->succeed = $succeed;
        $this->messages = $messages;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'succeed' => $this->succeed,
            'messages' => $this->messages,
            'data' => $this->data
        ];
    }
}
