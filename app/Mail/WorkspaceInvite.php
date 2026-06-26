<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Workspace;

class WorkspaceInvite extends Mailable
{
    use Queueable, SerializesModels;

    public $workspace;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct(Workspace $workspace, $email)
    {
        $this->workspace = $workspace;
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('You have been invited to join workspace: ' . $this->workspace->name)
                    ->view('emails.workspace_invite')
                    ->with([
                        'workspace' => $this->workspace,
                        'email' => $this->email,
                    ]);
    }
}