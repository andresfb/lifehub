<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseUserCommand;
use Exception;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

final class CreateApiTokenCommand extends BaseUserCommand
{
    protected $signature = 'create:token {--user=}';

    protected $description = 'Creates a User API token';

    public function handle(): int
    {
        try {
            clear();
            intro('Create an API token');

            $user = $this->loadUser();

            $clientName = text(
                label: 'Enter the Client Name',
                default: 'bruno',
                required: true,
                validate: 'string|max:50',
                transform: fn (string $value): string => mb_trim($value),
            );

            $user->tokens()
                ->where('name', $clientName)
                ->delete();

            $token = $user->createToken($clientName)->plainTextToken;

            warning("API token: {$token}");

            return self::SUCCESS;
        } catch (Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }
}
