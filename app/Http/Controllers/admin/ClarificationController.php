<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClarificationRequest;
use App\Models\Clarification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClarificationController extends Controller
{
    public function index()
    {
        try {
            $clarifications = Clarification::with(['report', 'user'])->orderBy('created_at', 'desc')->paginate(20);
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Klarifikasi tidak ditemukan',
              'data' => $e
            ], 404);
          }
          return response()->json([
            'status' => true,
            'data' => compact('clarifications')
          ], 200);
    }

    public function dashboard()
    {
      $report = Report::count();
      $clarification = Clarification::count();
      $user = User::count();
      return response()->json([
        'status' => true,
        'data' => compact('clarification', 'user', 'report')
      ], 200);

    }

    public function show($id)
    {
        try {
            $clarification = Clarification::with(['report', 'user'])->where('id', $id)->firstOrFail();
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Klarifikasi tidak ditemukan',
              'data' => $e
            ], 404);
          }
          return response()->json([
            'status' => true,
            'data' => compact('clarification')
          ], 200);
    }

    public function store(ClarificationRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $calrification_exist = Clarification::where('report_id',$request->report_id)->get()->first();
        if(isset($calrification_exist) && $calrification_exist != null){
          return throw new HttpResponseException(response()->json([
            'errors' => ['Aduan sudah di klarifikasi'],
            'status' => false ,
            'test' =>  $calrification_exist
        ], 422)); 
        }

        $images = [];
        $title_slug = Str::slug($request->title);

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
        if($request->hoax == 'true'){
          $hoax = true;
        }else {
          $hoax = false;
        }

        $clarification = Clarification::create([
            'user_id' => $user->id,
            'report_id' => $request->report_id,
            'title' => $request->title,
            'slug' => $title_slug,
            'content' => $request->content,
            'link' => $request->link,
            'images' => $images,
            'video' => $video_name,
            'hoax' => $hoax
        ]);
        $clarification->report->update([
          'clarified' => true,
          'hoax' =>  $hoax
        ]);

        return response()->json([
            'status' => true,
            'messages' => 'Klarifikasi berhasil disimpan',
            'data' => compact('clarification')
          ], 200);
    }

    public function update(ClarificationRequest $request, $id)
    {
      try {
        $clarification = Clarification::with(['report', 'user'])->where('id', $id)->firstOrFail();
      } catch (ModelNotFoundException $e) {
        return response()->json([
          'status' => false,
          'message' => 'Klarifikasi tidak ditemukan',
          'data' => $e
        ], 404);
      }
      // return response()->json([
      //   'status' => true,
      //   'messages' => 'Klarifikasi berhasil disimpan',
      //   'data' => $request->hoax
      // ], 200);

        $images = $clarification->images;
        $title_slug = Str::slug($request->title);
        $images = $clarification->images;
        if ($request->hasFile('images')) {
          if(isset($clarification->images)){
            foreach (json_decode($clarification->images) as $i=>$oldimg) {
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
        $video_name = $clarification->video;
        if($request->hoax == 'true'){
          $hoax = true;
        }else {
          $hoax = false;
        }
        if ($request->hasFile('video')) {
          if(isset($clarification->video)){
            $destiVideo = 'uploads/videos/' . $clarification->video;
                File::delete($destiVideo);
          }
            $video = $request->video;
            $destination_video = public_path('uploads/videos/');
            $video_name = $title_slug . substr(str_shuffle('0123456789'), 1, 2) . time() . '.' . $video->getClientOriginalExtension();
            $video->move($destination_video, $video_name);
        }

        $clarification->update([
            'title' => $request->title,
            'slug' => $title_slug,
            'content' => $request->content,
            'link' => $request->link,
            'images' => $images,
            'video' => $video_name,
            'hoax' => $hoax
        ]);
        $clarification->report->update([
          'clarified' => true,
          'hoax' =>  $hoax
        ]);

        return response()->json([
            'status' => true,
            'messages' => 'Klarifikasi berhasil disimpan',
            'data' => compact('clarification')
          ], 200);
    }

    public function destroy($id)
    {
        try {
            $clarification = Clarification::where('id', $id)->firstOrFail();
          } catch (ModelNotFoundException $e) {
            return response()->json([
              'status' => false,
              'message' => 'Klarifikasi tidak ditemukan',
              'data' => $e
            ], 404);
          }
          $clarification->report->update([
            'clarified' => false
          ]);
          $clarification->delete();
          return response()->json([
            'status' => true,
            'message' => 'Klarifikasi berhasil dihapus'
          ], 200);
    }
}
