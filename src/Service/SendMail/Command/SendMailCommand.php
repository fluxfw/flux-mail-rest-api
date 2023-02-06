<?php

namespace FluxMailRestApi\Service\SendMail\Command;

use DateTime;
use FluxMailRestApi\Adapter\Attachment\AttachmentDataEncoding;
use FluxMailRestApi\Adapter\Mail\EncryptionType;
use FluxMailRestApi\Adapter\Mail\MailDto;
use FluxMailRestApi\Adapter\Smtp\SmtpConfigDto;
use PHPMailer\PHPMailer\PHPMailer;

class SendMailCommand
{

    private function __construct(
        private readonly SmtpConfigDto $smtp_config
    ) {

    }


    public static function new(
        SmtpConfigDto $smtp_config
    ) : static {
        return new static(
            $smtp_config
        );
    }


    public function send(MailDto $mail) : void
    {
        $sender = null;
        try {
            $sender = new PHPMailer(true);

            $sender->isSMTP();
            $sender->Host = $this->smtp_config->host;
            $sender->Port = $this->smtp_config->port;

            if ($this->smtp_config->encryption_type === EncryptionType::TLS_AUTO) {
                $sender->SMTPSecure = EncryptionType::SSL;
                $sender->SMTPAutoTLS = true;
            } else {
                $sender->SMTPSecure = $this->smtp_config->encryption_type?->value;
                $sender->SMTPAutoTLS = false;
            }

            $sender->SMTPAuth = ($this->smtp_config->auth_type !== null || $this->smtp_config->user_name !== null || $this->smtp_config->password !== null);
            $sender->Username = $this->smtp_config->user_name;
            $sender->Password = $this->smtp_config->password;
            $sender->AuthType = $this->smtp_config->auth_type?->value;

            $sender->Subject = $mail->subject;

            $sender->isHTML();
            $sender->Body = $mail->body_html;
            $sender->AltBody = $mail->body_text;

            foreach ($mail->to as $to) {
                $sender->addAddress($to->email, $to->name);
            }

            foreach ($mail->attachments as $attachment) {
                $data = $attachment->data;
                switch ($attachment->data_encoding) {
                    case AttachmentDataEncoding::BASE64:
                        $data = base64_decode($data);
                        break;

                    case AttachmentDataEncoding::PLAIN:
                    default:
                        break;
                }
                $sender->addStringAttachment($data, $attachment->name, PHPMailer::ENCODING_BASE64, $attachment->data_type);
            }

            foreach ($mail->reply_to as $reply_to) {
                $sender->addReplyTo($reply_to->email, $reply_to->name);
            }

            foreach ($mail->cc as $cc) {
                $sender->addCC($cc->email, $cc->name);
            }

            foreach ($mail->bcc as $bcc) {
                $sender->addBCC($bcc->email, $bcc->name);
            }

            $from = $mail->from ?? $this->smtp_config->default_from;
            $sender->setFrom($from->email, $from->name);

            $sender->MessageDate = (new DateTime("@" . $mail->time))->format("D, j M Y H:i:s O");

            $sender->MessageID = $mail->message_id;

            $sender->send();
        } finally {
            $sender?->smtpClose();
        }
    }
}
