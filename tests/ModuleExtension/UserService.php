<?php

namespace App\Services;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\UserRepository;
use App\Repositories\UserSettingRepository;

class UserService
{    
    protected $mailer;
    protected $messager;
    protected $user_repository;
    protected $user_setting_repository;

    public function __construct(
        Mailer $mailer, 
        Message $messager, 
        UserRepository $user_repository,
        UserSettingRepository $user_setting_repository
    ) {
        $this->mailer = $mailer;
        $this->messager = $messager;
        $this->user_repository = $user_repository;
        $this->user_setting_repository = $user_setting_repository;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
//            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}

/* End of file UserService.php */
/* Location: .//home/tkb-user/projects/laravel/app/Services/UserService.php */