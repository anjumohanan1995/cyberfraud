<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMergePreview extends Mailable
{
    use Queueable, SerializesModels;

    public $sub;
    public $number;
    public $url;
    public $domain_name;
    public $domain_id;

    /**
     * Create a new message instance.
     *
     * @param string $sub The subject of the email
     * @param string $salutation The salutation in the email
     * @param string $compiledContent The compiled content of the email
     * @return void
     */
    public function __construct($sub, $number, $url, $domain_name, $domain_id)
    {
        // dd($sub);
        $this->sub = $sub;
        $this->number = $number;
        $this->url = $url;
        $this->domain_name = $domain_name;
        $this->domain_id = $domain_id;
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
                        'number' => $this->number,
                        'url' => $this->url,
                        'domain_name' => $this->domain_name,
                        'domain_id' => $this->domain_id,
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
