<?php

declare(strict_types=1);

namespace Inilim\Long\TransferEnv;

enum TransferEnvEnum: string
{
    case MAIN                 = '__ENV';
    case PATH_TO_DB           = 'PATH_TO_DB';
    case TIME_LEFT            = 'TIME_LEFT';
    case WORKER_ID            = 'WORKER_ID';
    case IS_SYS_WORKER        = 'IS_SYS_WORKER';
    case WORKER_START_PROCESS = 'WORKER_START_PROCESS';
    case WORKER_START_CREATE  = 'WORKER_START_CREATE';
}
