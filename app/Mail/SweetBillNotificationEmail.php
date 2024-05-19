<?php

namespace App\Mail;

use App\Models\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class SweetBillNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $emailMessage;
    public $subject;
    public $appName;
    public $emailRecipient;

    public function __construct($subject,$email_message,$emailRecipient)
    {
        //
        $this->emailMessage = $email_message;   
        $this->subject = $subject; 
        $this->emailRecipient = $emailRecipient; 
        $this->appName = SiteSettings::first()->name;  
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@sweetbill.ng', $this->appName),
            replyTo: [
                new Address('support@sweetbill.ng',$this->appName)
            ],
            subject: $this -> subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'EmailNotificationView',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
