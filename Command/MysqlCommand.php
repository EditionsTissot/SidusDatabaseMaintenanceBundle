<?php
/*
 * This file is part of the Sidus/DatabaseMaintenanceBundle package.
 *
 * Copyright (c) 2021 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sidus\DatabaseMaintenanceBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Query mysql with passthru command
 */
class MysqlCommand extends Command
{
    protected ManagerRegistry $doctrine;
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure(): void
    {
        $this
            ->setName('sidus:database:mysql')
            ->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'The name of the doctrine connection')
            ->setDescription('Command alias to mysql client with the proper parameters');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Connection $connection */
        $connection = $this->doctrine->getConnection($input->getOption('connection'));
        if ('mysql' !== $connection->getDatabasePlatform()->getName()) {
            throw new \LogicException('Only MySQL database is supported');
        }

        $host = escapeshellarg($connection->getHost());
        $port = $connection->getPort();
        $username = escapeshellarg($connection->getUsername());
        $password = escapeshellarg($connection->getPassword());
        $database = escapeshellarg($connection->getDatabase());

        $cmd = "mysql -h {$host} -P {$port} -u {$username} -p{$password} {$database}";
        passthru($cmd, $return);

        return $return;
    }
}
