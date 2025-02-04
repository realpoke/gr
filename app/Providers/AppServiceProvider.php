<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('whereAnyLike', function (string|array $attributes, string $searchTerm): Builder {
            return $this->where(function (Builder $query) use ($attributes, $searchTerm): void {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm): void {
                            [$relationName, $relationAttribute] = explode('.', $attribute);
                            if (method_exists($query->getModel(), $relationName)) {
                                $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm): void {
                                    $query->where($relationAttribute, 'LIKE', '%'.$searchTerm.'%');
                                });
                            } else {
                                $query->orWhere($attribute, 'LIKE', '%'.$searchTerm.'%');
                            }
                        },
                        function (Builder $query) use ($attribute, $searchTerm): void {
                            $query->orWhere($attribute, 'LIKE', '%'.$searchTerm.'%');
                        }
                    );
                }
            });
        });
    }
}
