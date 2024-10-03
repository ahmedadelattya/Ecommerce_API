<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;


class CouponController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'code' => 'required|string|unique:coupons,code|max:255',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'expiration_date' => 'nullable|date|after:today',
    ]);

    $coupon = Coupon::create($validated);

    return response()->json(['message' => 'Coupon created successfully!', 'coupon' => $coupon], 201);
}

public function update(Request $request, $id)
{
    $coupon = Coupon::findOrFail($id);

    $validated = $request->validate([
        'code' => 'required|string|unique:coupons,code,' . $coupon->id . '|max:255',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'expiration_date' => 'nullable|date|after:today',
    ]);

    $coupon->update($validated);

    return response()->json(['message' => 'Coupon updated successfully!', 'coupon' => $coupon], 200);
}

public function destroy($id)
{
    $coupon = Coupon::findOrFail($id);
    $coupon->delete();

    return response()->json(['message' => 'Coupon deleted successfully!'], 200);
}

public function index()
{
    $coupons = Coupon::all();
    return response()->json(['coupons' => $coupons], 200);
}

public function show($id)
{
    $coupon = Coupon::findOrFail($id);
    return response()->json(['coupon' => $coupon], 200);
}
}
