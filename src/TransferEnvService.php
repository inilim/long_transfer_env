<?php

declare(strict_types=1);

namespace Inilim\Long\TransferEnv;

use Inilim\Tool\Obj;
use Inilim\Tool\Str;
use Inilim\Tool\Json;
use Inilim\Tool\PF;

/**
 */
final class TransferEnvService
{
    const MAX_BITE_SIZE = 32_000;
    const MAIN_KEY = '__TRANSFER_ENV';

    /**
     * @return array<string,string>
     */
    function encode(mixed $data): array
    {
        $endata = null;
        // @INFO у windows есть ограничение при передачи данных в env proc_open
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            $len = \strlen($endata = \json_encode($data));

            if ($len > self::MAX_BITE_SIZE) {
                throw new \InvalidArgumentException(\sprintf('The environment block size (%d) exceeds the Windows limit of %d UTF-16 code units.', $len, self::MAX_BITE_SIZE));
                // throw new \Exception('[Windows] The data limit for transportation has been exceeded');
            }
        }

        /** @var null|string $endata */

        return [
            self::MAIN_KEY => ($endata ?? \json_encode($data)),
        ];
    }

    /**
     * @param mixed[] $data
     */
    function checkMaxSizeData(array $data): bool
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            return true;
        }
        return \strlen(\json_encode($data)) <= self::MAX_BITE_SIZE;
    }

    /** 
     * @param mixed[] $array default $_SERVER
     */
    function decodeFromArray(?array &$array = null, bool $clearAfterDecode = true): mixed
    {
        if ($array === null) {
            $array = &$_SERVER;
        }
        $key = self::MAIN_KEY;
        $env = $array[$key] ?? null;
        if (!\is_string($env)) {
            throw Obj::sprintfException('$array["%s"] not found or not string', [$key]);
        }
        if (!PF::json_validate($env)) {
            throw Obj::sprintfException('$array["%s"] not json ("%s")', [$key, Str::limit($env, 25)]);
        }
        if ($clearAfterDecode) {
            unset($array[$key]);
        }
        $env = Json::decode($env, true);
        return $env;
    }
}
