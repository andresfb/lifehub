<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Commands;

use App\Console\Commands\Base\BaseUserCommand;
use App\Domain\Bookmarks\Dtos\MarkerItem;
use App\Domain\Bookmarks\Models\Category;
use App\Domain\Bookmarks\Models\Marker;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;
use function Laravel\Prompts\warning;

final class CreateMarkerCommand extends BaseUserCommand
{
    protected $signature = 'create:marker
                            {--user=}
                            {--form= : Use a form to enter the bookmark info separately}';

    protected $description = 'Add a new URL Bookmark';

    private string $defaultCategory = '';

    private User $user;

    public function handle(): int
    {
        try {
            clear();
            intro('Add Bookmark');

            $this->user = $this->loadUser();

            warning('Creating Bookmark');

            $item = $this->option('form')
                ? $this->getFormData()
                : $this->getTextData();

            if (Marker::found($item->url, $this->user->id)) {
                throw new RuntimeException('URL already exists');
            }

            return DB::transaction(static function () use ($item): int {
                $marker = Marker::query()
                    ->create($item->toArray());

                if (blank($item->tags)) {
                    return self::SUCCESS;
                }

                $tags = array_map(trim(...), explode(',', $item->tags));
                $marker->attachTags($tags);

                return self::SUCCESS;
            });
        } catch (Throwable $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }

    private function getFormData(): MarkerItem
    {
        $this->checkCategories();

        $response = form()
            ->select(
                label: 'Select Category',
                options: Category::getSelectableList($this->user->id),
                default: $this->defaultCategory,
                scroll: 10,
                name: 'category',
            )
            ->text(
                label: 'Enter the Title',
                validate: 'string',
                hint: 'Title Titlest',
                name: 'title',
                transform: fn (string $value): string => mb_trim($value),
            )
            ->text(
                label: 'Enter the URL',
                required: true,
                validate: 'string|url|active_url',
                name: 'url',
                transform: fn (string $value): string => mb_trim($value),
            )
            ->text(
                label: 'Tags',
                validate: 'string',
                hint: 'Comma-separated',
                name: 'tags',
            )
            ->submit();

        return MarkerItem::from($response);
    }

    private function getTextData(): MarkerItem
    {
        $entry = textarea(
            label: 'Enter the full URL information',
            required: true,
            validate: 'string',
            hint: "Category\n  Title\n  URL\n  Tags (coma separated)",
        );

        $parts = collect(explode("\n", $entry));
        if ($parts->count() < 3) {
            throw new RuntimeException('Incomplete URL data');
        }

        $category = str($parts->shift())
            ->trim()
            ->title()
            ->toString();

        if (blank($category)) {
            throw new RuntimeException('No category provided');
        }

        $categoryId = Category::query()
            ->updateOrCreate([
                'title' => $category,
                'user_id' => Auth::id(),
            ])
            ->id;

        $title = str($parts->shift())
            ->trim()
            ->title()
            ->toString();

        if (blank($title)) {
            throw new RuntimeException('No title provided');
        }

        $url = str($parts->shift())
            ->trim()
            ->rtrim('/')
            ->toString();

        if (blank($url)) {
            throw new RuntimeException('No url provided');
        }

        $tags = '';
        if ($parts->count() > 0) {
            $tags = $parts->first();
        }

        return new MarkerItem(
            categoryId: $categoryId,
            title: $title,
            url: $url,
            tags: $tags,
        );
    }

    private function checkCategories(): void
    {
        if (Category::query()->count() > 0) {
            $this->askForCategory();

            return;
        }

        warning('No Categories Found');

        $this->enterCategory();
    }

    private function askForCategory(): void
    {
        if (! confirm('Enter new Category', false)) {
            return;
        }

        $this->enterCategory();
    }

    private function enterCategory(): void
    {
        $name = text(
            label: 'Category Name',
        );

        $category = Category::query()
            ->create([
                'title' => $name,
            ]);

        $this->defaultCategory = $category->title;
    }
}
