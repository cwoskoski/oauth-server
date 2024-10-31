<?php

namespace OAuthServer\Command;

use phpseclib3\Crypt\RSA;
use Hyperf\Command\Command;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class OAuthKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     */
    protected ?string $signature = 'oauth:key {--force : Overwrite keys they already exist}
                                      {--length=4096 : The length of the private key}';


    public function __construct(private ContainerInterface $container, private ConfigInterface $config)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $force = $this->input->getOption('force');
        $length = $this->input->getOption('length');
        $path = BASE_PATH . DIRECTORY_SEPARATOR . 'var';

        [$publicKey, $privateKey] = [
            $path . DIRECTORY_SEPARATOR . 'oauth-public.key',
            $path . DIRECTORY_SEPARATOR . 'oauth-private.key',
        ];

        if ((file_exists($publicKey) || file_exists($privateKey)) && ! $force) {
            $this->error('Encryption keys already exist. Use the --force option to overwrite them.');
        } else {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $private    = RSA::createKey($this->input ? (int) $length : 4096);
            $public     = $private->getPublicKey();

            file_put_contents($publicKey, $public);
            file_put_contents($privateKey, $private);

            $this->info('Encryption keys generated successfully.');
        }
    }

    protected function configure(): void
    {
        $this->setDescription('Create the encryption keys for API authentication');
    }
}
