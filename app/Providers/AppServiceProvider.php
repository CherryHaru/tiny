<?php

namespace App\Providers;


use Faker\Generator as Faker;
use Illuminate\Support\ServiceProvider;
use App\FakerProviders\Internet;
use App\FakerProviders\Image;
use DB;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use Log;
use League\Fractal\Manager as FractalManager;
use Storage;
use Carbon\Carbon;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            $faker = app(Faker::class);
            $faker->addProvider(new Internet($faker));
            $faker->addProvider(new Image($faker));
            DB::listen(function ($query) {
                $sql = str_replace('?', '%s', $query->sql);
                $sql = sprintf($sql, ...$query->bindings);
                Log::info('sql', [$sql, $query->time]);
            });
        }
        Carbon::setLocale('zh');
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
        $this->app->singleton(FractalManager::class, function ($app) {
            $manager = new FractalManager();
            $request = $app->make('request');
            if ($request->has('include')) {
                $manager->parseIncludes($request->get('include'));
            }
            return $manager;
        });

        $this->app->singleton(\League\Glide\Server::class, function ($app) {
            $config = config('images');
            return ServerFactory::create([
                'response' => new LaravelResponseFactory($this->app->make('request')),
                'source' => Storage::disk($config['source_disk'])->getDriver(),
                'cache' => Storage::disk($config['cache_disk'])->getDriver(),
                'source_path_prefix' => $config['source_path_prefix'],
                'cache_path_prefix' => $config['cache_path_prefix'],
                'base_url' => $config['base_url'],
                'presets' => $config['presets']
            ]);
        });
    }
}
