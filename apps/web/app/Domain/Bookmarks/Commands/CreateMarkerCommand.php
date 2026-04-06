<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Commands;

use App\Console\Commands\Base\BaseUserCommand;
use App\Domain\Bookmarks\Models\Category;
use App\Domain\Bookmarks\Models\Marker;
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
use function Laravel\Prompts\warning;

final class CreateMarkerCommand extends BaseUserCommand
{
    protected $signature = 'create:marker {--user=}';

    protected $description = 'Add a new URL Bookmark';

    private string $defaultCategory = '';

    public function handle(): int
    {
        try {
            clear();
            intro('Add Bookmark');

            $this->loadUser();
            $this->checkCategories();

            warning('Creating Bookmark');

            $result = form()
                ->select(
                    label: 'Select Category',
                    options: Category::getSelectableList(),
                    default: $this->defaultCategory,
                    scroll: 10,
                    name: 'category',
                )
                ->text(
                    label: 'Enter the Title',
                    validate: 'string',
                    hint: 'Title Titlest',
                    name: 'title',
                    transform: fn (string $value): string => trim($value),
                )
                ->text(
                    label: 'Enter the URL',
                    required: true,
                    validate: 'string|url|active_url',
                    name: 'url',
                    transform: fn (string $value): string => trim($value),
                )
                ->text(
                    label: 'Tags',
                    validate: 'string',
                    hint: 'Comma-separated',
                    name: 'tags',
                )
                ->submit();

            $urlHash = Marker::getHash($result['url']);
            if (Marker::query()->where('hash', $urlHash)->exists()) {
                throw new RuntimeException('URL already exists');
            }

            return DB::transaction(static function () use ($result): int {
                $marker = Marker::query()
                    ->create([
                        'category_id' => (int) $result['category'],
                        'title' => $result['title'],
                        'url' => $result['url'],
                    ]);

                if (blank($result['tags'])) {
                    return self::SUCCESS;
                }

                $tags = array_map(trim(...), explode(',', $result['tags']));
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
