@extends('layouts.master')

@section('title', 'پلن‌ها و تعرفه‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">انتخاب پلن</h4>

    <div class="row">
        @foreach($allPlans as $plan)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card h-100 {{ $currentPlan && $currentPlan->id == $plan->id ? 'border-primary' : '' }}">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ $plan->name }}</h5>
                    <p class="card-text text-muted">{{ $plan->description }}</p>

                    <div class="text-center my-3">
                        <span class="fs-4 fw-bold">{{ number_format($plan->monthly_price) }} تومان</span>
                        @if($plan->duration_days)
                        <small class="text-muted d-block">/ {{ $plan->duration_days }} روز</small>
                        @else
                        <small class="text-muted d-block">نامحدود</small>
                        @endif
                    </div>

                    
                    <ul class="list-unstyled">
                        @foreach($plan->features as $feature)
                        <li class="mb-2">
                            @if(is_string($feature) && \Str::contains($feature, '|'))
                            @php [$icon, $text] = explode('|', $feature, 2) @endphp
                            <i class="bx {{ $icon }} text-success me-2"></i> {{ $text }}
                            @else
                            <i class="bx bx-check text-success me-2"></i> {{ __("features.{$feature}") }}
                            @endif
                        </li>
                        @endforeach
                    </ul>

                    <hr>

                    <h6 class="fw-semibold">محدودیت‌ها</h6>
                    <ul class="list-unstyled small">
                        @foreach($plan->limits as $key => $value)
                        <li class="mb-1">
                            {{ __("limits.{$key}") }}:
                            <span class="fw-semibold">{{ $value ?? 'نامحدود' }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    @if($currentPlan && $currentPlan->id == $plan->id)
                    <button class="btn btn-secondary w-100" disabled>پلن فعلی</button>
                    @elseif(in_array($plan->id, $upgradableIds))
                    <form action="{{ route('billing.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="btn btn-primary w-100">ارتقا</button>
                    </form>
                    @else
                    <button class="btn btn-secondary w-100" disabled>در دسترس نیست</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection