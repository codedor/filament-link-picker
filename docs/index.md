# A custom field for Filament for picking routes

## Installation

Install this package using composer:

```bash
composer require codedor/filament-link-picker
```

In an effort to align with Filament's theming methodology you will need to use a custom theme to use this plugin.

> **Note**
> If you have not set up a custom theme and are using a Panel follow the instructions in the [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) first. The following applies to both the Panels Package and the standalone Forms package.

1. Import the plugin's stylesheet (if not already included) into your theme's css file.

```css
@import '../../../../vendor/codedor/filament-link-picker/resources/css/plugin.css';
```

2. Add the plugin's views to your `tailwind.config.js` file.

```js
content: [
    ...
    './vendor/codedor/filament-link-picker/resources/**/*.blade.php',
]
```

## Basic usage, simple routes without parameters

The package adds a `linkPicker()` macro to the Route facade of Laravel, this means you can use it in your routes files like this:

```php
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])
    ->name('home')
    ->linkPicker();
```

Important note: your route **must** have a `name` defined, otherwise the link picker will not work.

## Adding the 'external URL' link

If you want to use the "Exernal link" option in the link picker, you need to add a route like this, in your `AppServiceProvider`:

```php
LinkCollection::addExternalLink();
```

## The Link object

You can pass a callback to the `linkPicker()` function, this callback has one parameter called `$link`, this is a `Codedor\LinkPicker\Link` object. With this object you can configure the link to your needs.

For example, adding a label to make your link more readable in Filament:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])
    ->name('home')
    ->linkPicker(fn (Link $link) => $link->label('Homepage'));
```

## The Link object

### Schema

If you use route model binding in your controller, the link picker will automatically build its schema according to the parameters of the route. However, you can still define the schema yourself if you want to. For example:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('{page:slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->linkPicker(fn (Link $link) => $link->schema(fn () => [
            Filament\Forms\Components\Select::make('slug')
                ->options(Page::pluck('name', 'id'))
                ->multiple(),
        ])
    );
```

### Label

You can set a label for your link, this will be shown in the link picker in Filament. For example:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])
    ->name('home')
    ->linkPicker(fn (Link $link) => $link->label('Homepage'));
```

### Description

You can set a description for your link, this will be shown in the link picker in Filament. For example:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])
    ->name('home')
    ->linkPicker(fn (Link $link) => $link->description('The homepage of the website'));
```

### Group

You can group your links in Filament, this will make it easier to find them. For example:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'show'])
    ->name('home')
    ->linkPicker(fn (Link $link) => $link->group('Website'));
```

### Building routes

Say for example you have a route like this:

```php
use Illuminate\Support\Facades\Route;

Route::get('blog/{category:slug}/{post:slug}', [PostController::class, 'show'])
    ->name('post.show')
    ->linkPicker();
```

This route has two parameters but since the blogpost is linked to a category, there is no need to select the category in the link picker. You can use the `buildUsing` method to build the route for the link picker. For example:

```php
use Codedor\LinkPicker\Link;
use Illuminate\Support\Facades\Route;

Route::get('blog/{category:slug}/{post:slug}', [PostController::class, 'show'])
    ->name('post.show');
    ->linkPicker(fn (Link $link) => $link
        ->schema(fn () => [
            Filament\Forms\Components\Select::make('post')
                ->options(BlogPost::pluck('title', 'id'))
                ->multiple(),
        ])
        ->buildUsing(function (Link $link) {
            $post = BlogPost::find($link->getParameter('post'));

            return route($link->route, [
                'category' => $post->category,
                'post' => $post,
            ]);
        })
    );
```

## The Filament field

You can add the linkpicker field to your resource like this:

```php
use Codedor\LinkPicker\Filament\LinkPickerInput;

public static function form(Form $form): Form
{
    return $form->schema([
        LinkPickerInput::make('link'),
    ]);
}
```

## Reading the link picker route in the front-end

This package comes with a helper function called `lroute()`, with it you can read your link in the front-end like so:

```html
<a href="{{ lroute($page->route) }}">
    {{ $page->title }}
</a>
```

If you have enabled the route to open in a new tab, the helper will automatically add the `target="_blank"` attribute to the link as well.
