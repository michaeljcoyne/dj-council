<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DjProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'stage_name' => $this->stage_name,
            'specialty' => $this->specialty,
            'bio' => $this->bio,
            'experience' => $this->experience,
            'hourly_rate' => $this->hourly_rate,
            'minimum_booking_hours' => $this->minimum_booking_hours,
            'profile_image' => $this->profile_image,
            'instagram_url' => $this->instagram_url,
            'soundcloud_url' => $this->soundcloud_url,
            'website_url' => $this->website_url,
            'location' => $this->location,
            'is_featured' => $this->is_featured,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'average_rating' => $this->whenCounted('reviews', function () {
                return $this->reviews_avg_rating ?? $this->averageRating();
            }),
            'review_count' => $this->whenCounted('reviews', function () {
                return $this->reviews_count;
            }),
            // Only include relationships when they are loaded
            'user' => new UserResource($this->whenLoaded('user')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookings')),
        ];
    }
}
