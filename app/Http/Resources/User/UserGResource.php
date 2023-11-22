<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            "id"=>$this->resource->id,
            "name"=>$this->resource->name,
            "surname"=>$this->resource->surname,
            "email"=>$this->resource->email,
            "role"=>$this->resource->role,
            "profesion"=>$this->resource->profesion,
            "description"=>$this->resource->description,
            "isInstructor"=>$this->resource->isInstructor,
            "state"=>$this->resource->state ?? 1,
            "created_at"=>$this->resource->created_at->format("Y-m-d h:i:s"),
            // "avatar"=> $this->resource->avatar ? env("APP_URL")."storage/".$this->resource->avatar : null,
            "avatar"=> $this->resource->avatar ? env("APP_URL").$this->resource->avatar : null,
        ];
    }
}
