<?php

declare(strict_types=1);

namespace Inilim\Long\TransferEnv;

use Inilim\Tool\Obj;
use Inilim\Tool\Str;
use Inilim\Tool\Json;
use Inilim\Long\TransferEnv\TransferEnvEnum;

final class TransferEnvService
{
    const MAX_BITE_SIZE = 32_000;

    protected int $decodedCount = 0;

    function encode(array $data): array
    {
        if (!$this->checkMaxSizeData($data)) {
            throw new \Exception('The data limit for transportation has been exceeded');
        }
        return [
            TransferEnvEnum::MAIN->value => \json_encode($data),
        ];
    }

    function checkMaxSizeData(array $data): bool
    {
        return \strlen(\json_encode($data)) <= self::MAX_BITE_SIZE;
    }

    function getCountDecoded(): int
    {
        return $this->decodedCount;
    }

    function hasBeenDecoded(): bool
    {
        return $this->decodedCount > 0;
    }

    function decodeFromServer(bool $clearFromServer = true, ?array &$server = null): array
    {
        if ($server === null) {
            $server = &$_SERVER;
        }
        $key = TransferEnvEnum::MAIN->value;
        $env = $server[$key] ?? null;
        if (!\is_string($env)) {
            throw Obj::sprintfException('$_SERVER["%s"] not found', [$key]);
        }
        if (!Json::isJsonAsArrOrObj($env)) {
            throw Obj::sprintfException('$_SERVER["%s"] not json ("%s")', [$key, Str::limit($env, 25)]);
        }
        $this->decodedCount++;
        if ($clearFromServer) {
            unset($server[$key]);
        }
        $env = Json::decode($env, true);
        return $env;
    }
}
