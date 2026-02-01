<?php

namespace App\Shared\Application\Bus;

interface CommandBus
{
    /**
     * @return mixed|null Wynik handlera (np. id po Create), albo null dla void.
     */
    public function dispatch(object $command): mixed;
}
