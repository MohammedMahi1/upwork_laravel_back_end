<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\json;

class TransportController extends Controller
{

    // Jobs index method to retrieve all transport jobs for the authenticated user
    public function jobs()
    {
        $userId = Auth::user()->id;
        $jobs = Transport::where('user_id', $userId)->get();
        if($jobs->isEmpty()) {
            return response()->json(['message' => 'No jobs found for this user.'], 404);
        }
        return response()->json([
            "jobs"=>$jobs,
        ],200);
    }
    // Create a new transport job method
    public function storeJobs(Request $request){
        $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
        ]);
        $createdJob = Transport::create([
            'job_title' => $request->job_title,
            'job_description' => $request->job_description,
            'user_id' => Auth::user()->id,
        ]);
        $createdJob->save();
        return response()->json([
            'message' => 'Job created successfully.',
            'job' => $createdJob,
        ], 201);
    }

}
