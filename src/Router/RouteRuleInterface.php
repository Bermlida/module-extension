<?php

namespace Application\Models;

use Application\Core\ModelBase;
use Application\Models\Repositories\PostRepository;

class PostModel extends ModelBase
{
    private $post_repository = null;

    public function __construct()
    {
        parent::__construct();
        $this->post_repository = new PostRepository();
    }

    public function updateToAaaaa($update_value = null)
    {
        if(\is_string($update_value))
            $this->post_repository->updateAllAaaaa($update_value);
        else
            $this->post_repository->updateAllAaaaa();
    }
}

/* End of file PostModel.php */
/* Location: ./application/models/PostModel.php */