<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GuestController extends Controller
{
  public function landing()
  {
    $reports = Report::orderBy('created_at', 'desc')->where('clarified', true)->take(7)->get();
    $hoaxs = Report::orderBy('created_at', 'desc')->where('clarified', true)->where('hoax', true)->take(4)->get();
    $facts = Report::orderBy('created_at', 'desc')->where('clarified', true)->where('hoax', false)->take(4)->get();

    return response()->json([
        'status' => true,
        'data' => compact('reports', 'hoaxs', 'facts')
      ], 200);
  }

  public function index()
  {
      $reports = Report::orderBy('created_at', 'desc')->paginate(20);

      return response()->json([
          'status' => true,
          'data' => compact('reports')
        ], 200);
  }

  public function show($id)
  {
      try {
          $report = Report::with(['clarification', 'user'])->where('id', $id)->firstOrFail();
        } catch (ModelNotFoundException $e) {
          return response()->json([
            'status' => false,
            'message' => 'Aduan tidak ditemukan',
            'data' => $e
          ], 404);
        }
        return response()->json([
          'status' => true,
          'data' => compact('report')
        ], 200);
  }

  public function search($keyword)
  {
      try {
          $reports = Report::with(['clarification', 'user'])->where('title', 'like', '%' . $keyword . '%')->orderBy('created_at', 'desc')->paginate(20);
        } catch (ModelNotFoundException $e) {
          return response()->json([
            'status' => false,
            'message' => 'Aduan tidak ditemukan',
            'data' => $e
          ], 404);
        }
        return response()->json([
          'status' => true,
          'data' => compact('reports')
        ], 200);
  }
  
  public function downloadVideo($id)
  {
    $report = Report::where('id', $id)->firstOrFail();
    
    return response()->download(public_path('/uploads/videos/'.$report->video));
  }
}
