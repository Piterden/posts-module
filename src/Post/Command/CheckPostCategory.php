<?php namespace Anomaly\PostsModule\Post\Command;

use Anomaly\PostsModule\Post\Contract\PostInterface;
use Illuminate\Routing\Route;

/**
 * Class for check post category.
 */
class CheckPostCategory
{

    /**
     * Post entry
     *
     * @var PostInterface
     */
    protected $post;

    /**
     * Create an instance of CheckPostCategory class
     *
     * @param PostInterface $post
     */
    public function __construct(PostInterface $post)
    {
        $this->post = $post;
    }

    /**
     * Handle the command
     *
     * @param  Route     $route
     * @return boolean
     */
    public function handle(Route $route)
    {
        $category = $this->post->getCategory();

        return $category->getSlug() == $route->getParameter('category');
    }
}
