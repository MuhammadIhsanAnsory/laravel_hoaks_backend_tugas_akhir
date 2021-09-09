<?php

namespace App\Http\Controllers;

use App\Models\Clarification;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
      $reports = Report::where('clarified', true)->orderBy('created_at', 'desc')->paginate(2);

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
          $reports = Report::with(['clarification', 'user'])->where('title', 'like', '%' . $keyword . '%')->orderBy('created_at', 'desc')->paginate(2);
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

  public function sort($by)
  {
      try {
          $reports = Report::with(['clarification', 'user'])->where('hoax', $by)->orderBy('created_at', 'desc')->paginate(2);
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
  
  public function reportVideo($id)
  {
    $report = Report::findOrFail($id);
    
    // return Storage::get(public_path('uploads/videos/'.$report->video));
    return response()->download(public_path('uploads/videos/'.$report->video));
  }
  
  public function clarificationVideo($id)
  {
    $clarification = Clarification::findOrFail($id);
    // return Storage::get(public_path('uploads/videos/'.$clarification->video));
    return response()->download(public_path('uploads/videos/'.$clarification->video));
  }
}
