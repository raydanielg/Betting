<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Models\UpdateLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laramin\Utility\VugiChugi;

class SystemController extends Controller
{
    public function systemInfo(){
        $laravelVersion = app()->version();
        $timeZone = config('app.timezone');
        $pageTitle = 'Application Information';
        return view('admin.system.info',compact('pageTitle', 'laravelVersion','timeZone'));
    }

    public function optimize(){
        $pageTitle = 'Clear System Cache';
        return view('admin.system.optimize',compact('pageTitle'));
    }

    public function optimizeClear(){
        Artisan::call('optimize:clear');
        $notify[] = ['success','Cache cleared successfully'];
        return back()->withNotify($notify);
    }

    public function systemServerInfo(){
        $currentPHP = phpversion();
        $pageTitle = 'Server Information';
        $serverDetails = $_SERVER;
        return view('admin.system.server',compact('pageTitle', 'currentPHP', 'serverDetails'));
    }

    public function systemUpdate() {
        $pageTitle = 'System Updates';
        return view('admin.system.update',compact('pageTitle'));
    }


    public function systemUpdateProcess(){
        if (gs('system_customized')) {
            return response()->json([
                'status'=>'error',
                'message'=>[
                    'The system already customized. You can\'t update the project'
                ]
            ]);
        }


        if (version_compare(systemDetails()['version'],gs('available_version'),'==')) {
            return response()->json([
                'status'=>'info',
                'message'=>[
                    'The system is currently up to date'
                ]
            ]);
        }


        if(!extension_loaded('zip')){
            return response()->json([
                'status'=>'error',
                'message'=>[
                    'Zip Extension is required to update the system'
                ]
            ]);
        }

        $purchasecode = env('PURCHASECODE');
        if (!$purchasecode) {
            return response()->json([
                'status'=>'error',
                'message'=>[
                    'Invalid request. Please contact with support'
                ]
            ]);
        }

        $website = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");

        $response = CurlRequest::curlPostContent(VugiChugi::upman(),[
            'purchasecode'=>$purchasecode,
            'product'=>systemDetails()['name'],
            'version'=>systemDetails()['version'],
            'website'=>$website,
        ]);

        $response = json_decode($response);
        if($response->status == 'error'){
            return response()->json([
                'status'=>'error',
                'message'=>$response->message->error
            ]);
        }

        if($response->remark == 'already_updated'){
            return response()->json([
                'status'=>'info',
                'message'=>$response->message->success
            ]);
        }

        $directory = 'core/temp/';
        $files = [];
        foreach($response->data->files as $key => $fileUrl){

            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "Purchase-Code: $purchasecode"
                ]
            ];

            $context = stream_context_create($opts);
            $fileContent = file_get_contents($fileUrl,false,$context);

            if(@json_decode($fileContent)->status == 'error'){
                return response()->json([
                    'status'=>'error',
                    'message'=>@json_decode($fileContent)->message->error
                ]);
            }
            file_put_contents($directory.$key.'.zip',$fileContent);
            $files[$key] = $fileContent;
        }

        $fileNames = array_keys($files);
        foreach($fileNames as $fileName){
            $rand    = Str::random(10);
            $dir     = base_path('temp/' . $rand);
            $extract = $this->extractZip(base_path('temp/' . $fileName.'.zip'), $dir);

            if ($extract == false) {
                $this->removeDir($dir);
                return response()->json([
                    'status'=>'error',
                    'message'=>['Something went wrong while extracting the update']
                ]);
            }

            if (!file_exists($dir . '/config.json')) {
                $this->removeDir($dir);
                return response()->json([
                    'status'=>'error',
                    'message'=>['Config file not found']
                ]);
            }

            $getConfig = file_get_contents($dir . '/config.json');
            $config    = json_decode($getConfig);

            $this->removeFile($directory . '/' . $fileName.'.zip');

            $mainFile = $dir . '/update.zip';
            if (!file_exists($mainFile)) {
                $this->removeDir($dir);
                return response()->json([
                    'status'=>'error',
                    'message'=>['Something went wrong while patching the update']
                ]);
            }


            //move file
            $extract = $this->extractZip(base_path('temp/' . $rand . '/update.zip'), base_path('../'));
            if ($extract == false) {
                return response()->json([
                    'status'=>'error',
                    'message'=>['Something went wrong while extracting the update']
                ]);
            }



            //Execute database
            if (file_exists($dir . '/update.sql')) {
                $sql = file_get_contents($dir . '/update.sql');
                DB::unprepared($sql);
            }

            $updateLog = new UpdateLog();
            $updateLog->version = $config->version;
            $updateLog->update_log = $config->changes;
            $updateLog->save();

            $this->removeDir($dir);

        }
        Artisan::call('optimize:clear');
        return response()->json([
            'status'=>'success',
            'message'=>['System updated successfully']
        ]);
    }

    public function systemUpdateLog(){
        $pageTitle = 'System Update Log';
        $updates = UpdateLog::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.system.update_log',compact('pageTitle','updates'));
    }

    protected function extractZip($file, $extractTo)
    {
        $zip = new \ZipArchive;
        $res = $zip->open($file);
        if ($res != true) {
            return false;
        }

        for( $i = 0 ; $i < $zip->numFiles ; $i++ ) {
            if ( $zip->getNameIndex( $i ) != '/' && $zip->getNameIndex( $i ) != '__MACOSX/_' ) {
                $zip->extractTo( $extractTo, array($zip->getNameIndex($i)) );
            }
        }

        $zip->close();
        return true;
    }

    protected function removeFile($path)
    {
        $fileManager = new FileManager();
        $fileManager->removeFile($path);
    }

    protected function removeDir($location)
    {
        $fileManager = new FileManager();
        $fileManager->removeDirectory($location);
    }
}
