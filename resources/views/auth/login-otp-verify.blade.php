<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تایید موبایل - سامانه جامع انبارا</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/fonts/fontawesome.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/core.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/theme-default.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/rtl.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/css/pages/page-auth.css') }}">

    <style>
        .auth-cover-image {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }

        .otp-input {
            width: 55px !important;
            height: 55px !important;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }

        .countdown {
            font-size: 15px;
            font-weight: 600;
        }
    </style>

</head>

<body>

    <div class="authentication-wrapper authentication-cover">

        <div class="authentication-inner row m-0">

            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center">
                <div class="flex-row text-center mx-auto">
                    <img src="/bg-light.jpg" alt="Auth Cover Bg color" class="img-fluid authentication-cover-img"
                        data-app-light-img="/bg-light.jpg" data-app-dark-img="/bg-dark.jpg">
                </div>
            </div>
            <!-- /Left Text -->

            {{-- فرم --}}
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-4 p-sm-5">

                <div class="w-px-400 mx-auto">

                    <!-- Logo -->
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="" class="app-brand-link">
                            <img src="/logo-light.png" alt="Anbara Logo" style="max-width: 180px;height:auto;"
                                data-app-light-img="/logo-light.png" data-app-dark-img="/logo-dark.png">
                        </a>
                    </div> <!-- /Logo -->

                    <h5 class="mb-3" style="text-align:center">
                        تایید شماره موبایل
                    </h5>

                    <p class="mb-4" style="text-align:center">

                        کد ارسال شده به شماره

                        <strong dir="ltr">
                            {{ $mobile }}
                        </strong>

                        را وارد کنید.

                    </p>

                    @include('errors.error')

                    <form action="{{ route('login.otp.verify') }}" method="POST">

                        @csrf
@include('errors.error')
<div id="ajax-alert" class="alert d-none" role="alert">
</div>
                        <div class="mb-4">

                            <div class="d-flex justify-content-center" dir="ltr">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                                <input type="text" class="form-control otp-input mx-1" maxlength="1">

                            </div>

                            <input type="hidden" name="code" id="code">

                        </div>

                        <button type="submit" class="btn btn-primary w-100">

                            تایید موبایل

                        </button>

                    </form>

                    <div class="text-center mt-4">
                        زمان باقیمانده:
                        <span id="timer" class="countdown text-warning">

                            02:00

    </span>

                    </div>

                    <div class="text-center mt-3">
                        <button type="button" id="resend-btn" class="btn btn-label-secondary w-100 d-none">
                            ارسال مجدد کد
                        </button>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>

    <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
    
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenInput = document.getElementById('code');
    
            const timer = document.getElementById('timer');
            const resendBtn = document.getElementById('resend-btn');
    
            let expiresAt = {{ $expiresAt->timestamp }};
            let timerInterval = null;
    
            /*
            |--------------------------------------------------------------------------
            | OTP INPUTS
            |--------------------------------------------------------------------------
            */
            inputs.forEach((input, index) => {
    
                input.addEventListener('input', function() {
    
                    this.value = this.value.replace(/\D/g, '');
    
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
    
                    hiddenInput.value = [...inputs]
                        .map(item => item.value)
                        .join('');
                });
    
                input.addEventListener('keydown', function(e) {
    
                    if (
                        e.key === 'Backspace' &&
                        this.value === '' &&
                        index > 0
                    ) {
                        inputs[index - 1].focus();
                    }
                });
    
                input.addEventListener('paste', function(e) {
    
                    e.preventDefault();
    
                    const pastedText = (
                        e.clipboardData ||
                        window.clipboardData
                    ).getData('text');
    
                    if (!/^\d{6}$/.test(pastedText)) {
                        return;
                    }
    
                    inputs.forEach((item, i) => {
                        item.value = pastedText[i] ?? '';
                    });
    
                    hiddenInput.value = pastedText;
    
                    inputs[5].focus();
                });
            });
    
            /*
            |--------------------------------------------------------------------------
            | TIMER
            |--------------------------------------------------------------------------
            */
            function updateTimer() {
    
                const now = Math.floor(Date.now() / 1000);
                const remaining = expiresAt - now;
    
                if (remaining <= 0) {
    
                    timer.innerHTML = '00:00';
    
                    resendBtn.classList.remove('d-none');
    
                    clearInterval(timerInterval);
    
                    return;
                }
    
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
    
                timer.innerHTML =
                    String(minutes).padStart(2, '0') +
                    ':' +
                    String(seconds).padStart(2, '0');
            }
    
            function startTimer() {
    
                clearInterval(timerInterval);
    
                updateTimer();
    
                timerInterval = setInterval(updateTimer, 1000);
            }
    
            startTimer();
    
            /*
            |--------------------------------------------------------------------------
            | RESEND OTP
            |--------------------------------------------------------------------------
            */
            resendBtn.addEventListener('click', function() {
    
                resendBtn.disabled = true;
                resendBtn.innerHTML = 'در حال ارسال...';
    
                fetch("{{ route('login.otp.resend') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
    
                    if (data.success) {
                        console.log(data);
                        showAlert(
                            data.message,
                            'success'
                        );

                        expiresAt = data.expires_at;
    
                        resendBtn.classList.add('d-none');
    
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = 'ارسال مجدد کد';
    
                        startTimer();
    
                        return;
                    }
    
                    showAlert(
                        data.message,
                        'danger'
                    );
    
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'ارسال مجدد کد';
                })
                .catch(error => {
    
                    console.error(error);
    
                    showAlert(
                        'خطا در ارسال مجدد کد',
                        'danger'
                    );
    
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = 'ارسال مجدد کد';
                });
            });
    
        });
    
        function showAlert(message, type = 'success') {
    
            const alertBox = document.getElementById('ajax-alert');
    
            alertBox.className = `alert alert-${type}`;
    
            alertBox.innerHTML = message;
    
            alertBox.classList.remove('d-none');
    
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
    
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 500000);
        }
    </script>

</body>

</html>
