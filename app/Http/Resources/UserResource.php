<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Only include relationships when they are loaded
            'dj_profile' => new DjProfileResource($this->whenLoaded('djProfile')),
            'venues' => VenueResource::collection($this->whenLoaded('venues')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
            'playlists' => PlaylistResource::collection($this->whenLoaded('playlists')),
        ];
    }
}
