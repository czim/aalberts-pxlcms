<?php
namespace Aalberts\Loggers;

use Aalberts\Contracts\NoticesLoggerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * For logging things that should require some mild unconcerned attention.
 */
class NoticesLogger extends AbstractLogger implements NoticesLoggerInterface
{
    const LOGS_SUB_PATH = 'notices';
    const LOG_FILENAME  = 'notices.log';
    const MAX_FILES     = 0;


    /**
     * Create a logger instance
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if (null === $logger) {

            $directory = storage_path() . '/logs/' . self::LOGS_SUB_PATH;
            $path      = $directory . '/' . self::LOG_FILENAME;

            $this->makeSureDirectoryExists($directory);

            $logger = new Logger('Notices');

            $logger->pushHandler(
                new RotatingFileHandler($path, self::MAX_FILES)
            );
        }

        parent::__construct($logger);
    }
}
