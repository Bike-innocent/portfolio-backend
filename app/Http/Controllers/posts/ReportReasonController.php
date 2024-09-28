<?php

namespace App\Http\Controllers\posts;
use App\Models\ReportReason;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportReasonController extends Controller
{
    public function index()
    {
        $reportReasons = ReportReason::all();
        return response()->json($reportReasons);
    }

    public function store(Request $request)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $reportReason = new ReportReason();
        $reportReason->reason = $request->reason;
        $reportReason->save();

        return response()->json($reportReason, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $reportReason = ReportReason::findOrFail($id);
        $reportReason->reason = $request->reason;
        $reportReason->save();

        return response()->json($reportReason);
    }

    public function destroy($id)
    {
        $reportReason = ReportReason::findOrFail($id);
        $reportReason->delete();

        return response()->json(null, 204);
    }
}
