<?php

namespace App\Models;

use App\Events\SyncFinished;
use App\Events\SyncUpdated;
use App\Jobs\RunSyncTask;
use App\Services\Sync\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Sync extends Model
{
    use HasFactory;

    protected $fillable = ['started_at', 'finished_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];

    protected $with = ['tasks'];

    protected $appends = [
        'runtime', 'status_report'
    ];

    protected static function booted()
    {
        static::creating(fn(Sync $sync) => $sync->user_id = $sync->user_id ?? Auth::id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param \Illuminate\Support\Collection|Task[] $tasks
     * @return Sync
     */
    public static function start(): Sync
    {
        return Sync::create([
            'started_at' => Carbon::now()
        ]);
    }

    public function getRuntimeAttribute()
    {
        if($this->started_at && $this->finished_at) {
            return $this->started_at->diffInSeconds($this->finished_at);
        }
        return null;
    }

    public function cancel()
    {
        $this->pendingTasks->each(fn(SyncTask $syncTask) => $syncTask->setStatusAsCancelled());
    }

    public function getStatusReportAttribute()
    {
        return sprintf(
            '%u tasks ran: %u succeeded, %u failed and %u cancelled',
            $this->tasks()->count(),
            $this->tasks()->where('status', 'succeeded')->count(),
            $this->tasks()->where('status', 'failed')->count(),
            $this->tasks()->where('status', 'cancelled')->count(),
        );
    }

    public function finish()
    {
        $this->finished_at = Carbon::now();
        $this->save();
        SyncFinished::dispatch($this);
    }

    public function pendingTasks()
    {
        return $this->tasks()->whereIn('status', [
            'queued', 'processing'
        ]);
    }

    public function tasks()
    {
        return $this->hasMany(SyncTask::class);
    }

    public function withTask(Task $task, array $config): SYnc
    {
        $task = SyncTask::newTask($task, $this, $config);
        return $this;
    }

    public function scopeLastFive(Builder $query)
    {
        $query->latest()->limit(5);
    }

    public function dispatch()
    {
        $this->tasks->each(fn(SyncTask $task) => $task->dispatch());
    }

}
