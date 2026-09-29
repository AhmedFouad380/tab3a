<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\BranchResource;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends BaseApiController
{
    /**
     * Get Branches List (Ordered by nearest if latitude & longitude provided)
     */
    public function index(Request $request): JsonResponse
    {
        $lat = $request->latitude ?? $request->lat;
        $lng = $request->longitude ?? $request->lng;

        $branches = Branch::with('workingHours')
            ->where('is_active', true)
            ->where('allows_pre_order', true)
            ->get();

        if ($lat && $lng) {
            foreach ($branches as $branch) {
                if ($branch->latitude && $branch->longitude) {
                    $theta = $lng - $branch->longitude;
                    $dist = sin(deg2rad($lat)) * sin(deg2rad($branch->latitude)) +
                        cos(deg2rad($lat)) * cos(deg2rad($branch->latitude)) * cos(deg2rad($theta));
                    $dist = acos(min(max($dist, -1.0), 1.0));
                    $dist = rad2deg($dist);
                    $branch->distance_km = round($dist * 60 * 1.1515 * 1.609344, 1);
                } else {
                    $branch->distance_km = null;
                }
            }

            $branches = $branches->sortBy('distance_km')->values();
        }

        return $this->success(BranchResource::collection($branches));
    }

    /**
     * Show single branch details
     */
    public function show(int $id): JsonResponse
    {
        $branch = Branch::with('workingHours')->where('is_active', true)->find($id);

        if (!$branch) {
            return $this->error($this->getLocale() === 'en' ? 'Branch not found' : 'الفرع غير موجود', 404);
        }

        return $this->success(new BranchResource($branch));
    }
}
