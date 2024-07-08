<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MailMergePreview extends Mailable
{
    use Queueable, SerializesModels;

    public $sub;
    public $number;
    public $url;
    public $domain_name;
    public $domain_id;
    // public $mailerName; // Add this property to hold the mailer name

    /**
     * Create a new message instance.
     *
     * @param string $sub The subject of the email
     * @param string $number The number in the email
     * @param string $url The URL in the email
     * @param string $domain_name The domain name in the email
     * @param string $domain_id The domain ID in the email
    //  * @param string $mailerName The mailer name to use for 'from' address and 'from' name
     * @return void
     */
    public function __construct($sub, $number, $url, $domain_name, $domain_id)  //, $mailerName
    {
        // dd($sub);
        $this->sub = $sub;
        $this->number = $number;
        $this->url = $url;
        $this->domain_name = $domain_name;
        $this->domain_id = $domain_id;
        // $this->mailerName = $mailerName; // Assign mailer name to the property
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $viewName = $this->getViewName();


    // Retrieve 'from' address and 'from' name from the mailer configuration based on $this->mailerName
    // $fromAddress = config("mail.mailers.$this->mailerName.from.address");
    // $fromName = config("mail.mailers.$this->mailerName.from.name");
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
