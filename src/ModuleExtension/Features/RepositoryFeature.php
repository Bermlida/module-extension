<?php

namespace ModuleExtension\Features;

trait RepositoryFeature
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }


     /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function store(array $data)
    {
        // 驗證請求...

        $flight = new Flight;

        $flight->name = $request->name;

        $flight->save();
    }
}

/* End of file UserRepository.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/UserRepository.php */