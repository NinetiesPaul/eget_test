<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use App\Mail\TaskAssigned;
use App\Mail\TaskClosed;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $taskId;
    
    protected $mailTo;

    protected $closed;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $mailTo, int $taskId, bool $closed = false)
    {
        $this->taskId = $taskId;
        $this->mailTo = $mailTo;
        $this->closed = $closed;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = ($this->closed) ? new TaskClosed($this->taskId) : new TaskAssigned($this->taskId);
        Mail::to($this->mailTo)->send($email);
    }
}
