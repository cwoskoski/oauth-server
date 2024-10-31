<?php

namespace OAuthServer\Command;

use Carbon\Carbon;
use Hyperf\Command\Command;
use Hyperf\DbConnection\Db;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use function Hyperf\Config\config;


class PurgeTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected ?string $signature = 'oauth:purge {--force : purge all expires token}';

    public function __construct(private ContainerInterface $container, private ConfigInterface $config)
    {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle(): void
    {
        $force = $this->input->getOption('force');

        $this->clear();

        $this->info('All token revoked');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Purge all token expires & revoked');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function clear(): void
    {
        $now = Carbon::now()->subHours(7)->format('Y-m-d H:i:s');

        $token = Db::connection(config('oauth.provider', 'default'))
            ->table('oauth_access_tokens')
            ->where('expires_at', '<=', $now)
            ->orWhere('revoked', 1)
            ->delete();

        $refresh = Db::connection(config('oauth.provider', 'default'))
            ->table('oauth_refresh_tokens')
            ->where('expires_at', '<=', $now)
            ->orWhere('revoked', 1)
            ->delete();
    }
}