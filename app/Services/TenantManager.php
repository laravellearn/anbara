<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Company;
use App\Models\FiscalYear;
use App\Models\User;
use RuntimeException;

class TenantManager
{
    protected ?Tenant $tenant = null;
    protected ?Company $company = null;
    protected ?User $user = null;

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function requireTenant(): Tenant
    {
        if (! $this->tenant) {
            throw new RuntimeException('Tenant context is not set.');
        }
        return $this->tenant;
    }

    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function requireCompany(): Company
    {
        if (! $this->company) {
            throw new RuntimeException('Company context is not set.');
        }
        return $this->company;
    }

    public function getCompanyId(): ?int
    {
        return $this->company?->id;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    // در TenantManager
    protected ?FiscalYear $fiscalYear = null;

    public function setFiscalYear(?FiscalYear $fiscalYear): void
    {
        $this->fiscalYear = $fiscalYear;
        session(['current_fiscal_year_id' => $fiscalYear?->id]);
    }

    public function getFiscalYear(): ?FiscalYear
    {
        if ($this->fiscalYear) return $this->fiscalYear;
        $id = session('current_fiscal_year_id');
        if ($id) {
            $this->fiscalYear = FiscalYear::find($id);
        } else {
            $this->fiscalYear = FiscalYear::current();
        }
        return $this->fiscalYear;
    }
}
