<?php namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use App\Models\{
    Article,
    User,
    Comment
};

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('ud-article', function (User $user, ?Article $article) {

            return $user->id === $article->author_id;
        });

        Gate::define('d-comment', function (User $user, ?Comment $comment) {
            return $user->id === $comment->author_id;
        });
    }
}
