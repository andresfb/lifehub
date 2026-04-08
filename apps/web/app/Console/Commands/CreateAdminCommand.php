<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateAdminAction;
use App\Dtos\Profile\NewUserItem;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\warning;

final class CreateAdminCommand extends Command
{
    protected $signature = 'create:admin';

    protected $description = 'Creates the admin user';

    public function handle(CreateAdminAction $action): int
    {
        try {
            clear();
            intro('Create a new Admin user');

            $this->downloadAdminSeeders();

            $results = form()
                ->text(
                    label: 'Name',
                    default: Config::string('constants.admin_name'),
                    required: true,
                    name: 'name',
                )
                ->text(
                    label: 'Email',
                    default: Config::string('constants.admin_email'),
                    required: true,
                    validate: 'string|email|max:255',
                    name: 'email',
                )
                ->password(
                    label: 'Password',
                    required: true,
                    validate: 'string|min:10|max:255',
                    name: 'password',
                )->password(
                    label: 'Confirm Password',
                    required: true,
                    validate: 'string|min:8|max:255',
                    name: 'password_confirmation',
                )
                ->submit();

            if (User::query()->where('email', $results['email'])->exists()) {
                throw new RuntimeException('This email already exists');
            }

            if ($results['password'] !== $results['password_confirmation']) {
                throw new RuntimeException('Password does not match');
            }

            $token = $action->handle(
                NewUserItem::from($results)
            );

            warning("API Token: {$token}");

            return self::SUCCESS;
        } catch (Throwable $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }

    private function downloadAdminSeeders(): void
    {
        $this->downloadHomepage();

        $this->downloadSearch();
    }

    private function downloadHomepage(): void
    {
        $seeder = __DIR__.'/../../../app/Domain/Core/Database/Seeders/AdminHomepageSeeder.php';
        if (file_exists($seeder)) {
            return;
        }

        $cmd = sprintf('op read "op://LifeHub/Homepage/Seeder" --out-file "%s"', $seeder);
        $proc = Process::run($cmd);

        if ($proc->successful() && file_exists($seeder)) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Seeder file could not be created: %s', $proc->errorOutput()
            ),
            $proc->exitCode()
        );
    }

    private function downloadSearch(): void
    {
        $seeder = __DIR__.'/../../../app/Domain/Core/Database/Seeders/AdminSearchProviders.php';
        if (file_exists($seeder)) {
            return;
        }

        $cmd = sprintf('op read "op://LifeHub/Search/Seeder" --out-file "%s"', $seeder);
        $proc = Process::run($cmd);

        if ($proc->successful() && file_exists($seeder)) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Seeder file could not be created: %s', $proc->errorOutput()
            ),
            $proc->exitCode()
        );
    }
}
