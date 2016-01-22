<?php
namespace Amplify;

use Amplify\Options;
use Simplify\Renderable;
use Simplify\View;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends Renderable
{

    public $errorInfo;

    /**
     *
     * @var PHPMailer
     */
    protected $mail;

    /**
     *
     * @param string $subject            
     * @param string|array $to            
     * @param string $name            
     */
    public function send($subject, $to, $name = '')
    {
        $view = $this->getView();
        
        $text = $html = $view->render($this);
        
        $mail = $this->getMail();
        
        if (is_array($to)) {
            foreach ($to as $_to) {
                if (is_array($_to)) {
                    $mail->AddAddress($_to[0], $_to[1]);
                } else {
                    $mail->AddAddress($_to);
                }
            }
        } else {
            $mail->AddAddress($to, $name);
        }
        
        $mail->Subject = $subject;
        
        $mail->Body = $html;
        $mail->AltBody = $text;
        
        $sent = $mail->Send();
        
        if (! $sent) {
            $this->errorInfo = $mail->ErrorInfo;
            
            return false;
        }
        
        return true;
    }

    /**
     *
     * @return PHPMailer
     */
    protected function getMail()
    {
        if (! $this->mail) {
            $this->mail = new PHPMailer();
            
            if (Options::value('mail_smtp')) {
                $this->mail->IsSMTP();
                $this->mail->SMTPAuth = Options::value('mail_smtp_auth', true);
                $this->mail->AuthType = Options::value('mail_smtp_auth_type', 'LOGIN');
                $this->mail->Host = Options::value('mail_smtp_host');
                $this->mail->Port = Options::value('mail_smtp_port', 465);
                $this->mail->Username = Options::value('mail_smtp_user');
                $this->mail->Password = Options::value('mail_smtp_password');
            }
            
            $this->mail->From = Options::value('mail_from_email');
            $this->mail->Sender = Options::value('mail_from_email');
            $this->mail->FromName = Options::value('mail_from_name');
            
            $this->mail->CharSet = 'utf-8';
            $this->mail->Priority = 1;
        }
        return $this->mail;
    }
}