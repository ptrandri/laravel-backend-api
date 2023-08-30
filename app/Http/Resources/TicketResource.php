<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return parent::toArray($request);
    // }
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'AgentName' => $this->AgentName,
            'SubjectCase' => $this->SubjectCase,
            'CallerName' => $this->CallerName,
            'CallerEmail' => $this->CallerEmail,
            'Status' => $this->Status,
            'Priority' => $this->Priority,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
