<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\FinishingOptionResource;
use App\Http\Resources\Api\V1\PricingRuleResource;
use App\Models\FinishingOption;
use App\Models\PricingRule;
use Illuminate\Http\JsonResponse;

class PricingController extends BaseApiController
{
    /**
     * Get All Pricing Rules & Finishing Options
     */
    public function index(): JsonResponse
    {
        $pricingRules = PricingRule::where('is_active', true)->get();
        $finishingOptions = FinishingOption::where('is_active', true)->get();

        return $this->success([
            'pricing_rules' => PricingRuleResource::collection($pricingRules),
            'finishing_options' => FinishingOptionResource::collection($finishingOptions),
        ]);
    }
}
