<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\SendOtpMail;
use App\Models\EmailOtp;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        EmailOtp::create([
            'email' => $request->email,
            'otp_code' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully.'],200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required'
        ]);

        $otpEntry = EmailOtp::where('email', $request->email)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpEntry) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }
        $otpEntry->delete();

        return response()->json(['verified'=>true],200);
    }
}
