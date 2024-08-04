<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentialEstateResource extends JsonResource
{
    public $status;
    public $message;
    public $statusCode;

    /**
     * __construct
     *
     * @param mixed $status
     * @param mixed $message
     * @param mixed $resource
     * @param int $statusCode
     * @return void
     */
    public function __construct($status, $message, $resource, $statusCode = 200)
    {
        parent::__construct($resource);
        $this->status  = $status;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Check if the resource is a paginator instance
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return [
                'success' => $this->status,
                'message' => $this->message,
                'data'    => [
                    'current_page' => $this->resource->currentPage(),
                    'data'         => $this->resource->items(),
                    'first_page_url' => $this->resource->url(1),
                    'from'         => $this->resource->firstItem(),
                    'last_page'    => $this->resource->lastPage(),
                    'last_page_url' => $this->resource->url($this->resource->lastPage()),
                    'next_page_url' => $this->resource->nextPageUrl(),
                    'path'         => $this->resource->path(),
                    'per_page'     => $this->resource->perPage(),
                    'prev_page_url' => $this->resource->previousPageUrl(),
                    'to'           => $this->resource->lastItem(),
                    'total'        => $this->resource->total(),
                ],
                'status' => $this->statusCode,
            ];
        }

        return [
            'success' => $this->status,
            'message' => $this->message,
            'data'    => [
                'id'          => $this->resource->id,
                'image'       => $this->resource->image,
                'housing_name'=> $this->resource->housing_name,
                'unit_type'   => new UnitTypeResource($this->whenLoaded('unitType')),
                'description' => $this->resource->description,
                'size'        => $this->resource->size,
                'location'    => $this->resource->location,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'status'  => $this->statusCode,
        ];
    }
}
