<?php

declare(strict_types=1);

namespace Inilim\Long\TransferEnv;

use Inilim\Tool\Obj;
use Inilim\Tool\Str;
use Inilim\Tool\Json;

/**
 * @INFO ограничение для windows
 */
final class TransferEnvService
{
    const MAX_BITE_SIZE = 32_000;
    const MAIN_KEY = '__TRANSFER_ENV';

    protected int $decodedCount = 0;

    /**
     * @param mixed[] $data
     * @return array<string,string>
     */
    function encode(array $data): array
    {
        if (!$this->checkMaxSizeData($data)) {
            throw new \Exception('The data limit for transportation has been exceeded');
        }
        return [
            self::MAIN_KEY => \json_encode($data),
        ];
    }

    /**
     * @param mixed[] $data
     */
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

    /** 
     * @param mixed[] $array default $_SERVER
     * @return mixed[]
     */
    function decodeFromArray(?array &$array = null, bool $clearAfterDecode = true): array
    {
        if ($array === null) {
            $array = &$_SERVER;
        }
        $key = self::MAIN_KEY;
        $env = $array[$key] ?? null;
        if (!\is_string($env)) {
            throw Obj::sprintfException('$array["%s"] not found or not string', [$key]);
        }
        if (!Json::isJsonAsArrOrObj($env)) {
            throw Obj::sprintfException('$array["%s"] not json ("%s")', [$key, Str::limit($env, 25)]);
        }
        $this->decodedCount++;
        if ($clearAfterDecode) {
            unset($array[$key]);
        }
        $env = Json::decode($env, true);
        return $env;
    }
}
