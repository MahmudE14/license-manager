<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\License;
use App\LicenseType;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LicenseController extends Controller
{
    public function create()
    {
        $types = LicenseType::all();
        return view('license.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required',
            'license_key' => 'required',
            'expire_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation_failed', 'message' => $validator->getMessageBag()]);
        }

        try {
            $license = License::create([
                'user_id'       => $request->user_id,
                'license_key'   => $request->license_key,
                'expire_date'   => Carbon::now()->addMonths($request->expire_date)->toDateString(),
            ]);

            return response()->json(['status' => 'success', 'message' => ['Success']]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => [$th]]);
        }
    }

    public function verify(Request $request)
    {
        $license_key = $request->license_key;

        $validator = Validator::make($request->all(), [
            'license_key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'validation_failed', 'message' => $validator->getMessageBag()]);
        }

        // check availability
        $license_available = License::where('user_id', Auth::user()->id)->where('license_key', $license_key)->first();

        if ($license_available->count()) {
            $user = User::where('id', Auth::user()->id)->first()
                        ->update([
                            'license_key' => $license_available->license_key,
                            'expire_date' => $license_available->expire_date,
                        ]);
        }

        if ($user) {
            $expire_date = User::where('id', Auth::user()->id)->first()->expire_date;
            return response()->json(['status' => 'success', 'message' => [$expire_date]]);
        }

        return response()->json(['status' => 'error', 'message' => ['An error occurred!']]);
    }

    public function createKey(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required',
            'months' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->getMessageBag()]);
        }

        $months = $request->months;
        $user_id = $request->user_id;
        $present_time = Carbon::now()->addMonths($months)->toDateString();
        $id = str_pad($user_id, 4, "0", STR_PAD_LEFT);
        $date_key = $present_time . "-" . $id;
        $key = "";

        for ($i=0; $i < strlen($date_key); $i++) {
            if ($date_key[$i] != "-") {
                $key .= strtoupper(chr(100 + intval($date_key[$i])));
            } else {
                $key .= $date_key[$i];
            }
        }

        return $key;
    }

    public function showVerify()
    {
        return view('license.verify');
    }

    public function getUserDetails(Request $request)
    {
        return response()->json(User::findOrFail($request->user_id));
    }
}
