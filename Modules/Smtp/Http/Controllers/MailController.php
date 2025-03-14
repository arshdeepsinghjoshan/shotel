<?php

/**
 *@copyright : ASk. < http://arshresume.epizy.com/ >
 *@author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 *
 * All Rights Reserved.
 * Proprietary and confidential :  All information contained herein is, and remains
 * the property of ASK. and its partners.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 */

namespace Modules\Smtp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Smtp\Http\Models\MailConfiguration;
use Modules\Smtp\Http\Models\SmtpEmailQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Notification\Http\Models\Notification;
use Illuminate\Support\Facades\File;

class MailController extends Controller
{
    public static function sendMail($view, $userModel)
    {
        try {
            $config = MailConfiguration::findActive()->latest()->first();
            $filePath = resource_path('views/emails/new_user.blade.php');
            $content = File::get($filePath);
            $content = str_replace('{{ $model->name }}', $userModel->name, $content);
            $content = str_replace('{{ $model->otp_email }}', $userModel->otp_email, $content);
            $content = str_replace('{{ url("/user/confirm-email/" . $model->activation_key) }}', url('user/confirm-email/' . $userModel->activation_key), $content);
            $emailData = [
                'from' => $userModel->email,
                'to' => env('MAIL_FROM_ADDRESS', false),
                'subject' => 'Your verification code.',
                'cc' => '',
                'bcc' => '',
                'content' => $content,
                'type_id' => null,
                'model_id' => $userModel->id,
                'state_id' => SmtpEmailQueue::STATE_PENDING,
            ];
            $smtpEmailQueue = new SmtpEmailQueue($emailData);
            $smtpEmailQueue->save();
            if ($config) {
                config([
                    'mail.mailers.smtp.host' => $config->host,
                    'mail.mailers.smtp.port' => $config->port,
                    'mail.mailers.smtp.username' => $config->username,
                    'mail.mailers.smtp.password' => $config->password,
                    'mail.mailers.smtp.encryption' => $config->encryption,
                    'mail.from.address' => $config->from_address,
                    'mail.from.name' => env('APP_NAME', false),
                ]);
                Mail::send($view, ['model' => $userModel], function ($message) use ($userModel) {
                    $message->to($userModel->email);
                    $message->subject('Your verification code.');
                });
                $smtpEmailQueue->state_id = SmtpEmailQueue::STATE_SENT;
                $smtpEmailQueue->save();
            }
            return true;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return false;
        }
    }
}
