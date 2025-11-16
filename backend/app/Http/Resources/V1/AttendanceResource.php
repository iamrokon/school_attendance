<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Attendance Resource for API v1.
 * Standalone copy of the previous AttendanceResource logic, using V1 StudentResource.
 */
class AttendanceResource extends JsonResource
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
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student', function () {
                return new StudentResource($this->student);
            }),
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'note' => $this->note,
            'recorded_by' => $this->recorded_by,
            'recorder' => $this->whenLoaded('recorder', function () {
                return [
                    'id' => $this->recorder->id,
                    'name' => $this->recorder->name,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
