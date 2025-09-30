<?php

namespace App\Support;

use App\Models\Business;

class BusinessContext
{
    public function get(): ?Business
    {
        return app('currentBusiness', null);
    }
    public function id(): ?int
    {
        return $this->get()?->id;
    }
}
