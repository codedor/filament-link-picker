<?php

namespace Codedor\LinkPicker;

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
