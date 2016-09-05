<?php

namespace Application\Models\Repositories;

use Application\Core\RepositoryBase;
use Application\Models\Entities\Post;

class PostRepository extends RepositoryBase
{
    public function updateAllAaaaa($newContent = 'default')
    {        
        $posts = Post::all();
        foreach($posts as $post )
        {
            $post->aaaaa = $newContent;
            $post->save();
        }
    }
}

/* End of file PostRepository.php */
/* Location: ./application/repositories/PostRepository.php */