<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentProfile;
use App\PaymentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class PaymentProfilesController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $profiles = QueryBuilder::for(PaymentProfile::class)
      ->defaultSort('name')
      ->ofTenant($tenant->id);

    $data = isset($request->paginate) ? $profiles->paginate($request->paginate) : $profiles->get();

    return response()->json($data, 200);
  }

  /**
   * Create the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\PaymentProfile  $payment_profile
   * @return \Illuminate\Http\Response
   */
  public function create(StorePaymentProfile $request)
  {
    $tenant = Auth::user()->tenant()->first();

    $payment_profile = new PaymentProfile();

    $payment_profile->fill($request->all());

    $payment_profile->save();

    return response()->json($payment_profile, 200);
  }
}
