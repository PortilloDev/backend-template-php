<?php

namespace App\Comic\Domain\Contracts;

interface ComicStoreInterface
{
    /** @return array [
     *      'publisher' => "MARVEL COMICS",
     *      'description' => "Showdown on Wall Street!",
     *      'title' => "CAPTAIN AMERICA SAM WILSON #6",
     *      'release_date' => "2016-02-03",
     * ] */
    public function all(): array;
}
