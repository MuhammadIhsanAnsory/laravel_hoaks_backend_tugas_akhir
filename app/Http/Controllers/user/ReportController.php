<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportController extends Controller
{
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $reports = Report::with(['user'])->where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(20);
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
            $user = JWTAuth::parseToken()->authenticate();
            $report = Report::with('clarification')->where('user_id', $user->id)->where('id', $id)->firstOrFail();
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

    public function store(ReportRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $title_slug = Str::slug($request->title);
        $images = '';
        if ($request->hasFile('images')) {
          $imgs = $request->images;
          foreach ($imgs as $i=>$img) {
              $file = $img;
              $destination = public_path('uploads/images/');
              $image_name = $title_slug . $i . substr(str_shuffle('0123456789'), 1, 2) . '.' . $img->getClientOriginalExtension();
              $file->move($destination, $image_name);
              
              $imagesName[] = $image_name;
          }
          $images = json_encode($imagesName);
      }
      $video_name = null;
      if ($request->hasFile('video')) {
          $video = $request->video;
          $destination_video = public_path('uploads/videos/');
          $video_name = $title_slug . substr(str_shuffle('0123456789'), 1, 2) . '.' . $video->getClientOriginalExtension();
          $video->move($destination_video, $video_name);
      }

      $report = Report::create([
          'user_id' => $user->id,
          'title' => $request->title,
          'slug' => $title_slug,
          'content' => $request->content,
          'link' => $request->link,
          'images' => $images,
          'video' => $video_name,
          'clarified' => false
      ]);

      return response()->json([
          'status' => true,
          'messages' => 'Aduan berhasil disimpan',
          'data' => compact('report')
      ], 200);
    }

    public function update(ReportRequest $request, $id)
    {
      try {
        $user = JWTAuth::parseToken()->authenticate();
        $report = Report::where('user_id', $user->id)->where('id', $id)->firstOrFail();
      } catch (ModelNotFoundException $e) {
        return response()->json([
          'status' => false,
          'message' => 'Aduan tidak ditemukan',
          'data' => $e
        ], 404);
      }

        $title_slug = Str::slug($request->title);
        $images = $report->images;
        if ($request->hasFile('images')) {
          if(isset($report->images)){
            foreach (json_decode($report->images) as $i=>$oldimg) {
              $desti = 'uploads/images/' . $oldimg;
              File::delete($desti);
            }
          }
          $imgs = $request->images;
          foreach ($imgs as $i=>$img) {
              $file = $img;
              $destination = public_path('uploads/images/');
              $image_name = $title_slug . $i . substr(str_shuffle('0123456789'), 1, 2) . time() . '.' . $img->getClientOriginalExtension();
              $file->move($destination, $image_name);
              
              $imagesName[] = $image_name;
          }
          $images = json_encode($imagesName);
      }
      $video_name = $report->video;
      if ($request->hasFile('video')) {
        if(isset($report->video)){
          $destiVideo = 'uploads/videos/' . $report->video;
              File::delete($destiVideo);
        }
          $video = $request->video;
          $destination_video = public_path('uploads/videos/');
          $video_name = $title_slug . substr(str_shuffle('0123456789'), 1, 2) . time() . '.' . $video->getClientOriginalExtension();
          $video->move($destination_video, $video_name);
      }

      $report->update([
          'title' => $request->title,
          'slug' => $title_slug,
          'content' => $request->content,
          'link' => $request->link,
          'images' => $images,
          'video' => $video_name,
      ]);

      return response()->json([
          'status' => true,
          'messages' => 'Aduan berhasil diupdate',
          'data' => compact('report')
      ], 200);
    }

    public function destroy($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $report = Report::where('user_id', $user->id)->where('id', $id)->firstOrFail();
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Aduan tidak ditemukan',
              'data' => $e
            ], 404);
          }
          if($report->clarified == 1 || $report->clarified == true){
            return response()->json([
              'status' => false,
              'message' => 'Aduan tidak bisa dihapus',
              'data' => $e
            ], 422);
          }
          $report->delete();
          return response()->json([
            'status' => true,
            'message' => 'Aduan berhasil dihapus'
          ], 200);
    }

}
