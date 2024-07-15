<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMergePreview extends Mailable
{
    use Queueable, SerializesModels;

    public $notices;

    /**
     * Create a new message instance.
     *
     * @param array $notices Array of notices to be sent
     */
    public function __construct($notices)
    {
        // dd($notices);
        $this->notices = $notices;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject($this->notices[0]['sub']) // Assuming the first notice determines the subject
                    ->view($this->getViewName())
                    ->with('notices', $this->notices);
    }

    /**
     * Determine the appropriate view based on notices.
     *
     * @return string
     */
    protected function getViewName()
    {
        // Determine view based on the first notice type (assuming all notices have the same view)
        switch ($this->notices[0]['sub']) {
            case 'Notice U/s 91 CrPC & 79(3)(b) of IT Act':
                return 'emails.91crpc_79itact';
            case 'Notice U/s 91 CrPC':
                return 'emails.91crpc';
            case 'Notice U/s 79(3)(b) of IT Act':
                return 'emails.79itact';
            default:
                return 'emails.default';
        }
    }
}
