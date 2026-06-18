<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\TenantManager;

class BaseController extends Controller
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }
}