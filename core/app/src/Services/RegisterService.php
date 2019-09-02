<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use RedBeanPHP\R as R;

class RegisterService
{
    private $emailService;
    private $appSettings;

    public function __construct(ContainerInterface $container) 
    {
        $this->emailService = $container->get('emailService');
        $this->appSettings = $container->get('settings')['app'];
    }

    public function registerUser($mail, $password, $displayName)
    {
        if(!is_null(R::findOne('user', 'mail = ?', [$mail])))
        {
            throw new \Exception("User already registered", 409);
        }

        $user = R::dispense('user');
        $user->mail = $mail;
        $user->display_name = $displayName;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $id = R::store($user);

        $this->sendRegistrationMail($id);
    }

    public function confirmRegistration($token)
    {
        $user = R::findOne('user', 'registration_token = ?', [$token]);
        
        if(is_null($user))
        {
            throw new \Exception("Pending registration not found", 404);
        }

        if(filter_var($user->mail_confirmed, FILTER_VALIDATE_BOOLEAN))
        {
            throw new \Exception("User already confirmed", 409);
        }

        if($user->registration_token_expires_at < time()) 
        {
            throw new \Exception("Registration token expired", 403);
        }

        $user->mail_confirmed = true;

        R::store($user);
    }

    public function resendRegistrationMail($mail)
    {
        $user = R::findOne('user', 'mail = ?', [$mail]);

        if(is_null($user))
        {
            throw new \Exception("User not registered", 404);
        }

        if(filter_var($user->mail_confirmed, FILTER_VALIDATE_BOOLEAN))
        {
            throw new \Exception("User already confirmed", 409);
        }

        $this->sendRegistrationMail($user->id);
    }

    private function sendRegistrationMail(int $userId)
    {
        $user = R::findOne('user', 'id = ?', [$userId]);

        if(is_null($user))
        {
            throw new \Exception("User not found", 500);
        }

        $user->registration_token = md5(uniqid($user->mail, true));
        $user->registration_token_expires_at = time() + (24 * 3600);
        $user->mail_confirmed = false;
        R::store($user);

        $subject = 'Your registration at ' . $this->appSettings['name'] . ', ' . $user->displayName . '!';
        $body = 'Hello <b>'
                .$user->displayName
                .'</b>,<br><br>welcome to '
                . $this->appSettings['name']
                .'! Click <a href="'
                .$this->appSettings['frontendBaseUrl']
                .'/login/'
                .$user->registration_token
                .'">here</a> to confirm your registration!<br><br>You have 24h to activate your account.<br><br>Have a nice day!';
        
        if(!$this->emailService->sendMail($user->mail, $user->displayName, $subject, $body))
        {
            throw new \Exception("Sending registration confirmation mail failed", 500);
        }
    }
}