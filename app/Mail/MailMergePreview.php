<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMergePreview extends Mailable
{
    use Queueable, SerializesModels;

    public $sub;
    public $salutation;
    public $compiledContent;

    /**
     * Create a new message instance.
     *
     * @param string $sub The subject of the email
     * @param string $salutation The salutation in the email
     * @param string $compiledContent The compiled content of the email
     * @return void
     */
    public function __construct($sub, $salutation, $compiledContent)
    {
        // dd($sub);
        $this->sub = $sub;
        $this->salutation = $salutation;
        $this->compiledContent = $compiledContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $viewName = $this->getViewName();
        // dd($viewName);

        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject($this->sub)
                    ->view($viewName)
                    ->with([
                        'sub' => $this->sub,
                        'salutation' => $this->salutation,
                        'compiledContent' => $this->compiledContent,
                    ]);
    }

    /**
     * Determine the appropriate view based on $sub.
     *
     * @return string
     */
    protected function getViewName()
    {
        switch ($this->sub) {
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
