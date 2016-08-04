<?php

namespace App\Repositories;

use App\Repositories\Entities\UserSetting;

class UserSettingRepository
{
    protected $user_setting;

    public function __construct(UserSettings $user_setting)
    {
        $this->user_setting = $user_setting;
    }
}

/* End of file UserSettingRepository.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/UserSettingRepository.php */