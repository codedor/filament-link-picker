<?php

namespace Codedor\LinkPicker;

use App\Models\BlogPost;
use Codedor\FilamentArchitect\Engines\Architect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Drawer\ImplicitRouteBinding;
use Livewire\Features\SupportPageComponents\SupportPageComponents;
use Livewire\Livewire;
use ReflectionClass;
use Throwable;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class LinkCollection extends Collection
{
    public function addLink(Link $link): self
    {
        $this->add($link);

        return $this;
    }

    public function addGroup(string $group, iterable $links): self
    {
        foreach ($links as $link) {
            $this->add($link->group($group));
        }

        return $this;
    }

    public function addExternalLink(
        string $routeName = 'external',
        string $group = 'General',
        string $label = 'External URL',
        string $description = 'Redirects to an external URL',
    ): self {
        return $this->addLink(
            Link::make($routeName, $label)
                ->group($group)
                ->description($description)
                ->schema(fn () => TextInput::make('url')->prefix('https://')->required())
                ->buildUsing(function (Link $link) {
                    $url = $link->getParameter('url');

                    return Str::startsWith($url, 'http') ? $url : "https://{$url}";
                })
        );
    }

    public function addEmailLink(
        string $routeName = 'email',
        string $group = 'General',
        string $label = 'Send e-mail',
        string $description = 'Opens the e-mail client',
    ): self {
        return $this->addLink(
            Link::make($routeName, $label)
                ->group($group)
                ->description($description)
                ->schema(fn () => TextInput::make('email')->label('Target e-mail')->email()->required())
                ->buildUsing(function (Link $link) {
                    $email = $link->getParameter('email');

                    return "mailto:{$email}";
                })
        );
    }

    public function addAnchorLink(
        string $routeName = 'anchor',
        string $group = 'General',
        string $label = 'Anchor link',
        string $description = 'Link to achor on current page',
    ): self {
        return $this->addLink(
            Link::make($routeName, $label)
                ->group($group)
                ->description($description)
                ->schema(function () {
                    return Select::make('anchor')
                        ->label('Anchor')
                        ->options(function (?Model $record) {
                            if (! $record) {
                                try {
                                    $request = Request::create(request()->header('referer'));
                                    $route = Route::getRoutes()->match($request);

                                    /** @var Page $component */
                                    $component = Str::replace('@__invoke', '', $route->action['uses']);

                                    $resource = $component::getResource();
                                    $model = $resource::getModel();

                                    $record = $model::find($route->parameter('record'));
                                } catch (Throwable $e) {
                                    return [];
                                }
                            }

                            if (! $record) {
                                return [];
                            }

                            if (method_exists($record, 'anchorList')) {
                                return $record->anchorList();
                            }

                            if (class_exists(Architect::class )) {
                                return collect($record?->getFillable())
                                    ->map(fn ($field) => $record->getAttributeValue($field))
                                    ->filter(fn ($value) => $value instanceof Architect)
                                    ->map->anchorList()
                                    ->dump()
                                    ->flatMap(fn ($values) => $values);
                            }

                            return [];
                        })
                        ->required();
                })
                ->buildUsing(function (Link $link) {
                    $anchor = $link->getParameter('anchor');

                    return "#{$anchor}";
                })
        );
    }

    /**
     * Returns a flattened collection of all routes.
     *
     * @return static<TKey, TValue>
     */
    public function routes(): self
    {
        return $this->flatten();
    }

    public function route(string $routeName): ?Link
    {
        return $this->routes()->first(function (Link $link) use ($routeName) {
            return $link->getRouteName() === $routeName;
        });
    }

    public function cleanRoute(string $routeName): ?Link
    {
        return $this->routes()->first(function (Link $link) use ($routeName) {
            return $link->getCleanRouteName() === $routeName;
        });
    }

    public function firstByCleanRouteName(string $routeName)
    {
        return $this->first(fn (Link $link) => $link->getCleanRouteName() === $routeName);
    }
}
