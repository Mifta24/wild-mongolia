<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyPointsAndCouponsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'points_to_use' => 'nullable|integer|min:0',
            'coupon_code' => 'nullable|string|exists:coupons,code',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'booking_id.required' => 'Booking ID is required',
            'booking_id.exists' => 'Invalid booking ID',
            'points_to_use.integer' => 'Points must be a valid number',
            'points_to_use.min' => 'Points must be at least 0',
            'coupon_code.exists' => 'Invalid coupon code',
        ];
    }
}
