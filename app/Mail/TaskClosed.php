<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskClosed extends Mailable
{
    use Queueable, SerializesModels;

    protected $taskNumber;

    public function __construct(int $taskNumber)
    {
        $this->taskNumber = $taskNumber;
    }

    public function build()
    {
        return $this->view('mail.closed', [ 'taskNumber' => $this->taskNumber ]);
    }
}
