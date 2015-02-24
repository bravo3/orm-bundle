<?php
namespace Bravo3\OrmBundle\Services;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

class OutputLogger extends AbstractLogger
{
    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        if ($level == LogLevel::ALERT ||
            $level == LogLevel::CRITICAL ||
            $level == LogLevel::EMERGENCY ||
            $level == LogLevel::ERROR
        ) {
            $this->output->writeln('<error>'.$message.'</error>');
        } elseif ($level == LogLevel::DEBUG) {
            $this->output->writeln('<info>'.$message.'</info>');
        } elseif ($level == LogLevel::WARNING) {
            $this->output->writeln('<comment>'.$message.'</comment>');
        } else {
            $this->output->writeln($message);
        }
    }
}
