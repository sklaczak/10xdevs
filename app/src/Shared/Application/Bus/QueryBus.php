<?php


namespace App\Shared\Application\Bus;

interface QueryBus
{
    /**
     * @return mixed Wynik query (DTO, lista DTO, itp.)
     */
    public function ask(object $query): mixed;
}
