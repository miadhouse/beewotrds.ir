<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        // ایجاد یک نمونه جدید از PHPMailer
        $this->mailer = new PHPMailer(true);

        // تنظیمات SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'persian.techfact@gmail.com'; // نام کاربری SMTP
        $this->mailer->Password = 'xsta rsnb fugs vwzo';    // کلمه عبور SMTP
        $this->mailer->SMTPSecure = 'tls';                  // استفاده از رمزنگاری TLS
        $this->mailer->Port = 587;                          // پورت اتصال

        // تنظیم فرمت ایمیل به HTML
        $this->mailer->isHTML(true);

        // تنظیم آدرس فرستنده
        $this->mailer->setFrom('persian.techfact@gmail.com', 'Beewords.ir');
    }

    public function sendEmail($toEmail, $subject, $body)
    {
        try {
            // افزودن گیرنده
            $this->mailer->addAddress($toEmail);

            // موضوع ایمیل
            $this->mailer->Subject = $subject;

            // محتوای ایمیل
            $this->mailer->Body = $body;

            // ارسال ایمیل
            if ($this->mailer->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            // مدیریت خطا در صورت عدم ارسال ایمیل
            error_log("email not send: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
