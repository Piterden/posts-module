<?php namespace Anomaly\PostsModule;

use Anomaly\PostsModule\Category\CategoryModel;
use Anomaly\PostsModule\Category\CategoryRepository;
use Anomaly\PostsModule\Category\Contract\CategoryRepositoryInterface;
use Anomaly\PostsModule\Http\Controller\Admin\AssignmentsController;
use Anomaly\PostsModule\Http\Controller\Admin\FieldsController;
use Anomaly\PostsModule\Post\Contract\PostRepositoryInterface;
use Anomaly\PostsModule\Post\PostModel;
use Anomaly\PostsModule\Post\PostRepository;
use Anomaly\PostsModule\Type\Contract\TypeRepositoryInterface;
use Anomaly\PostsModule\Type\TypeRepository;
use Anomaly\Streams\Platform\Addon\AddonServiceProvider;
use Anomaly\Streams\Platform\Assignment\AssignmentRouter;
use Anomaly\Streams\Platform\Field\FieldRouter;
use Anomaly\Streams\Platform\Model\Posts\PostsCategoriesEntryModel;
use Anomaly\Streams\Platform\Model\Posts\PostsPostsEntryModel;

/**
 * Class PostsModuleServiceProvider
 *
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 *
 * @link          http://pyrocms.com/
 */
class PostsModuleServiceProvider extends AddonServiceProvider
{

    /**
     * The class bindings.
     *
     * @var array
     */
    protected $bindings = [
        PostsPostsEntryModel::class      => PostModel::class,
        PostsCategoriesEntryModel::class => CategoryModel::class,
    ];

    /**
     * The singleton bindings.
     *
     * @var array
     */
    protected $singletons = [
        PostRepositoryInterface::class     => PostRepository::class,
        TypeRepositoryInterface::class     => TypeRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'admin/posts'                        => 'Anomaly\PostsModule\Http\Controller\Admin\PostsController@index',
        'admin/posts/choose'                 => 'Anomaly\PostsModule\Http\Controller\Admin\PostsController@choose',
        'admin/posts/create'                 => 'Anomaly\PostsModule\Http\Controller\Admin\PostsController@create',
        'admin/posts/edit/{id}'              => 'Anomaly\PostsModule\Http\Controller\Admin\PostsController@edit',
        'admin/posts/view/{id}'              => 'Anomaly\PostsModule\Http\Controller\Admin\PostsController@view',
        'admin/posts/categories'             => 'Anomaly\PostsModule\Http\Controller\Admin\CategoriesController@index',
        'admin/posts/categories/create'      => 'Anomaly\PostsModule\Http\Controller\Admin\CategoriesController@create',
        'admin/posts/categories/edit/{id}'   => 'Anomaly\PostsModule\Http\Controller\Admin\CategoriesController@edit',
        'admin/posts/categories/view/{id}'   => 'Anomaly\PostsModule\Http\Controller\Admin\CategoriesController@view',
        'admin/posts/categories/assignments' => 'Anomaly\PostsModule\Http\Controller\Admin\CategoriesController@assignments',
        'admin/posts/types'                  => 'Anomaly\PostsModule\Http\Controller\Admin\TypesController@index',
        'admin/posts/types/create'           => 'Anomaly\PostsModule\Http\Controller\Admin\TypesController@create',
        'admin/posts/types/edit/{id}'        => 'Anomaly\PostsModule\Http\Controller\Admin\TypesController@edit',
    ];

    /**
     * Map the addon.
     *
     * @param FieldRouter      $fields
     * @param AssignmentRouter $assignments
     */
    public function map(FieldRouter $fields, AssignmentRouter $assignments)
    {
        $fields->route($this->addon, FieldsController::class);
        $assignments->route($this->addon, AssignmentsController::class);
    }

    /**
     * Generate posts routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        // $settings = app(SettingRepositoryInterface::class);
        // $posts_uri = $settings->value('anomaly.module.settings::config.posts_uri');
        $posts_uri = 'articles';

        return array_merge($this->routes, [
            $posts_uri . '/rss/categories/{category}.xml' => [
                'as'   => 'anomaly.module.posts::categories.rss',
                'uses' => 'Anomaly\PostsModule\Http\Controller\RssController@category',
            ],
            $posts_uri . '/rss/tags/{tag}.xml'            => [
                'as'   => 'anomaly.module.posts::tags.rss',
                'uses' => 'Anomaly\PostsModule\Http\Controller\RssController@tag',
            ],
            $posts_uri . '/rss.xml'                       => [
                'as'   => 'anomaly.module.posts::posts.rss',
                'uses' => 'Anomaly\PostsModule\Http\Controller\RssController@recent',
            ],
            $posts_uri                                    => [
                'as'   => 'anomaly.module.posts::posts.index',
                'uses' => 'Anomaly\PostsModule\Http\Controller\PostsController@index',
            ],
            $posts_uri . '/preview/{str_id}'              => [
                'as'   => 'anomaly.module.posts::posts.preview',
                'uses' => 'Anomaly\PostsModule\Http\Controller\PostsController@preview',
            ],
            $posts_uri . '/tags/{tag}'                    => [
                'as'   => 'anomaly.module.posts::tags.view',
                'uses' => 'Anomaly\PostsModule\Http\Controller\TagsController@index',
            ],
            $posts_uri . '/{slug}'                        => [
                'as'   => 'anomaly.module.posts::categories.view',
                'uses' => 'Anomaly\PostsModule\Http\Controller\CategoriesController@index',
            ],
            $posts_uri . '/archive/{year}/{month?}'       => [
                'as'   => 'anomaly.module.posts::tags.archive',
                'uses' => 'Anomaly\PostsModule\Http\Controller\ArchiveController@index',
            ],
            $posts_uri . '/{category}/{slug}'             => [
                'as'   => 'anomaly.module.posts::posts.view',
                'uses' => 'Anomaly\PostsModule\Http\Controller\PostsController@view',
            ],
        ]);
    }
}
