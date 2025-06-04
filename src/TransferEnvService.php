<?php

declare(strict_types=1);

namespace Inilim\Long\TransferEnv;

use Inilim\Tool\Json;
use Inilim\Long\TransferEnv\TransferEnvEnum;

final class TransferEnvService
{
    function encode(array $data): array
    {
        return [
            TransferEnvEnum::MAIN->value => \json_encode($data),
        ];
    }

    function decode(string $data): array
    {
        if (!Json::isJsonAsArrOrObj($data)) {
            throw new \InvalidArgumentException('not json');
        }
        return Json::decode($data, true);
    }

    function decodeFromServer(bool $clearFromServer = true): array
    {
        $key = TransferEnvEnum::MAIN->value;
        $env = $_SERVER[$key] ?? null;
        if (!\is_string($env) || !Json::isJsonAsArrOrObj($env)) {
            throw new \InvalidArgumentException('$_SERVER key "__ENV" not found or not json');
        }
        if ($clearFromServer) {
            unset($_SERVER[$key]);
        }
        $env = Json::decode($env, true);
        return $env;
    }
}
