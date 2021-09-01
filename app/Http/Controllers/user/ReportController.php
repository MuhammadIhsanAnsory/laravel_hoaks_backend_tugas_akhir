<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportController extends Controller
{
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $reports = Report::with(['user'])->where('user_id', $user->id)->paginate(20);
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
        $images = [];
        $title_slug = Str::slug($request->title);
        // return response()->json([
        //   'status' => true,
        //   'messages' => 'Aduan berhasil disimpan',
        //   'data' => $request->title
        // ], 200);

        if ($request->hasFile('images')) {
            $imgs = $request->file('images');
            foreach ($imgs as $i=>$img) {
                $file = $img;
                $destination = public_path('uploads/images/');
                $image_name = $title_slug . $i . substr(str_shuffle('0123456789'), 1, 2) . '.' . $img->getClientOriginalExtension();
                $file->move($destination, $image_name);
                
                $imagesName[] = $image_name;
            }

            $images = json_encode($imagesName);

        }
        $video_name = '';

        if ($request->hasFile('video')) {
            $video = $request->file('video');
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
          $report->delete();
          return response()->json([
            'status' => true,
            'message' => 'Aduan berhasil dihapus'
          ], 200);
    }

    public function downloadVideo($id)
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
      return response()->download(public_path('/uploads/videos/'.$report->video));
    }
}
