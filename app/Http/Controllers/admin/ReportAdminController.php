<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportAdminController extends Controller
{
    public function index()
    {
        try {
            $reports = Report::with(['user'])->orderBy('created_at', 'desc')->paginate(20);
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

    public function show($id)
    {
        try {
            $report = Report::with(['user', 'clarification'])->where('id', $id)->firstOrFail();
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

    public function destroy($id)
    {
        try {
            $report = Report::where('id', $id)->firstOrFail();
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Aduan tidak ditemukan',
              'data' => $e
            ], 404);
          }
          $report->delete();
          return response()->json([
            'status' => true,
            'message' => 'Aduan berhasil dihapus'
          ], 200);
    }

    public function trash()
    {
        try {
            $reports = Report::with('user')->onlyTrashed()->paginate(20);
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

    public function restore($id)
    {
        try {
            $report = Report::onlyTrashed()->findOrFail($id);
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Aduan tidak ditemukan',
              'data' => $e
            ], 404);
          }
          $report->restore();
          return response()->json([
            'status' => true,
            'message' => 'Aduan berhasil direstore',
        ], 200);
    }

    public function forceDelete($id)
    {
        try {
            $report = Report::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Aduan tidak ditemukan',
              'data' => $e
            ], 404);
        }
          $report->forceDelete();
          return response()->json([
            'status' => true,
            'message' => 'Aduan sudah dihapus permanen',
        ], 200);
    }
}
