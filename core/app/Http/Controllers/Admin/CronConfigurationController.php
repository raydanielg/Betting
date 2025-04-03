<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CronJob;
use App\Models\CronJobLog;

class CronConfigurationController extends Controller
{
    public function cronJobs()
    {
        $pageTitle = 'Cron Jobs';
        $crons     = CronJob::with('logs')->get();
        return view('admin.cron.index', compact('pageTitle', 'crons'));
    }

    public function scheduleLogs($id)
    {
        $cronJob   = CronJob::findOrFail($id);
        $pageTitle = $cronJob->name . " Cron Schedule Logs";
        $logs      = CronJobLog::where('cron_job_id', $cronJob->id)->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.cron.logs', compact('pageTitle', 'logs', 'cronJob'));
    }

    public function scheduleLogResolved($id)
    {
        $log        = CronJobLog::findOrFail($id);
        $log->error = null;
        $log->save();

        $notify[] = ['success', 'Cron log resolved successfully'];
        return back()->withNotify($notify);
    }

    public function logFlush($id)
    {
        $cronJob = CronJob::findOrFail($id);
        CronJobLog::where('cron_job_id', $cronJob->id)->delete();

        $notify[] = ['success', 'All logs flushed successfully'];
        return back()->withNotify($notify);
    }
}
