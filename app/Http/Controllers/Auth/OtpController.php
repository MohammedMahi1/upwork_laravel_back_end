<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\SendOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function sendOtp()
    {
        $email = Auth::user()->email;

        $otpEntry = EmailOtp::where('email', $email)
            ->latest()
            ->first();
        if ($otpEntry) {
            $otpEntry->delete();
        }
        $otp = rand(100000, 999999);
        EmailOtp::create([
            'email' => $email,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully.'], 200);
    }

    public function verifyOtp(Request $request)
    {

        $request->validate([
            'otp_code' => 'required'
        ]);
        $email = Auth::guard('sanctum')->user()->email;
        $user = User::where('email', $email)->first();

        if ($user->is_verify) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $otpEntry = EmailOtp::where('email', $email)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpEntry) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }
        $user->is_verify = true;
        $user->email_verified_at = Carbon::now();
        $user->save();
        $otpEntry->delete();
        return response()->json(['verified' => true], 200);
    }
}
