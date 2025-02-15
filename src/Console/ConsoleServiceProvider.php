<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Console;

use Flarum\Database\Console\MigrateCommand;
use Flarum\Database\Console\ResetCommand;
use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Foundation\Console\AssetsPublishCommand;
use Flarum\Foundation\Console\CacheClearCommand;
use Flarum\Foundation\Console\InfoCommand;
use Illuminate\Console\Scheduling\Schedule as LaravelSchedule;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Used by Laravel to proxy artisan commands to its binary.
        // Flarum uses a similar binary, but it's called flarum.
        if (! defined('ARTISAN_BINARY')) {
            define('ARTISAN_BINARY', 'flarum');
        }

        $this->container->singleton(LaravelSchedule::class, function () {
            return $this->container->make(Schedule::class);
        });

        $this->container->singleton('flarum.console.commands', function () {
            return [
                AssetsPublishCommand::class,
                CacheClearCommand::class,
                InfoCommand::class,
                MigrateCommand::class,
                ResetCommand::class,
                ScheduleListCommand::class,
                ScheduleRunCommand::class
            ];
        });

        $this->container->singleton('flarum.console.scheduled', function () {
            return [];
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $schedule = $this->container->make(LaravelSchedule::class);

        foreach ($this->container->make('flarum.console.scheduled') as $scheduled) {
            $event = $schedule->command($scheduled['command'], $scheduled['args']);
            $scheduled['callback']($event);
        }
    }
}
