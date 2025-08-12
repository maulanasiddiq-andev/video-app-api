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
    public bool $succeedd;
    public array $messages;
    public mixed $data;

    public function __construct($succeed, $messages, $data)
    {
        $this->succeedd = $succeed;
        $this->messages = $messages;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'succeed' => $this->succeedd,
            'messages' => $this->messages,
            'data' => $this->data
        ];
    }
}
