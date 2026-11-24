<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ translate('Provider Login') }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="robots" content="nofollow, noindex ">
    @php($favIcon = getBusinessSettingsImageFullPath(key: 'business_favicon', settingType: 'business_information', path: 'business/', defaultPath: 'public/assets/admin-module/img/placeholder.png'))
    <link rel="shortcut icon" href="{{ $favIcon }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <link href="{{ asset('public/assets/provider-module') }}/css/material-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/assets/provider-module') }}/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ asset('public/assets/provider-module') }}/css/toastr.css">

    <style>
        :root {
            --primary: #FAD293;
            --primary-dark: #D4A76A;
            --bg-dark: #0A0A0A;
            --card-bg: rgba(20, 20, 20, 0.7);
            --gold-gradient: linear-gradient(135deg, #FAD293 0%, #D4A76A 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            padding: 1.5rem 1rem;
            box-sizing: border-box;
        }

        .login-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('{{ asset('public/assets/admin-module/img/automotive-login-bg.png') }}');
            background-size: cover;
            background-position: center;
            z-index: -1;
            transform: scale(1.05);
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(250, 210, 147, 0.2);
            border-radius: 24px;
            padding: 2.75rem 2.25rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
            margin: auto;
            position: relative;
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem 0.75rem;
                align-items: center;
            }

            .glass-card {
                padding: 1.75rem 1.25rem;
                border-radius: 18px;
                max-width: 100%;
            }

            .login-logo-container {
                margin-bottom: 1.25rem !important;
            }

            .login-logo {
                max-width: 110px !important;
            }

            .form-title {
                font-size: 1.45rem !important;
            }

            .form-subtitle {
                margin-bottom: 1.25rem !important;
                font-size: 0.85rem !important;
            }

            .input-premium {
                padding: 0.9rem 1rem 0.9rem 3rem !important;
                font-size: 0.95rem !important;
            }

            .btn-premium {
                padding: 0.85rem !important;
                margin-top: 1.25rem !important;
                font-size: 0.95rem !important;
            }

            .form-group-premium {
                margin-bottom: 1rem !important;
            }

            .software-version {
                position: static !important;
                text-align: center;
                margin-top: 1rem;
                padding-bottom: 0.5rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            max-width: 140px;
            filter: drop-shadow(0 0 10px rgba(250, 210, 147, 0.3));
        }

        .form-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .form-subtitle {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 0.95rem;
        }

        .form-group-premium {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .input-premium:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 15px rgba(250, 210, 147, 0.2) !important;
            outline: none;
        }

        .input-premium::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.4rem;
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-premium {
            background: var(--gold-gradient);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            color: #000;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            margin-top: 2rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(250, 210, 147, 0.3);
            filter: brightness(1.1);
        }

        .form-footer {
            margin-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .togglePassword {
            position: absolute;
            right: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.2rem;
        }

        .software-version {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .toast {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--primary) !important;
            color: white !important;
        }
    </style>
</head>

<body>
    <div class="login-bg"></div>

    <div class="w-100 d-flex flex-column align-items-center justify-content-center my-auto">
        <div class="glass-card">
            @php($logo = getBusinessSettingsImageFullPath(key: 'business_logo', settingType: 'business_information', path: 'business/', defaultPath: 'public/assets/admin-module/img/placeholder.png'))

            <div class="login-logo-container">
                <img src="{{ $logo }}" class="login-logo" alt="MMC">
            </div>

            <h1 class="form-title" id="login-title">{{ translate('Provider Sign In') }}</h1>
            <p class="form-subtitle">{{ translate('Premium Automotive Terminal') }}</p>

            <!-- Role Selector Toggle -->
            <div class="d-flex mb-4 p-1" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(250, 210, 147, 0.25); border-radius: 14px;">
                <button type="button" class="btn flex-grow-1 role-toggle-btn active-role-btn" id="btn-login-provider" data-role="provider" style="border-radius: 10px; font-weight: 700; font-size: 0.9rem; padding: 10px 14px; background: var(--gold-gradient); color: #000; border: none; transition: all 0.3s ease;">
                    <span class="material-icons align-middle" style="font-size: 18px; margin-right: 4px;">business</span>
                    {{ translate('Provider') }}
                </button>
                <button type="button" class="btn flex-grow-1 role-toggle-btn" id="btn-login-partner" data-role="partner" style="border-radius: 10px; font-weight: 600; font-size: 0.9rem; padding: 10px 14px; background: transparent; color: rgba(255, 255, 255, 0.65); border: none; transition: all 0.3s ease;">
                    <span class="material-icons align-middle" style="font-size: 18px; margin-right: 4px;">handshake</span>
                    {{ translate('Partner') }}
                </button>
            </div>

            <input type="hidden" name="login_type" id="login_type" value="provider">

            <!-- OTP Login Form (Default) -->
            <div id="otp-login-container">
                <form id="otp-login-form">
                    @csrf
                    <!-- Step 1: Email Input -->
                    <div class="form-group-premium">
                        <span class="material-icons input-icon">email</span>
                        <input type="email" name="otp_email" id="otp_email" class="form-control input-premium"
                            value="{{ request()->cookie('provider_remember_email') }}"
                            placeholder="{{ translate('Enter your Registered Email') }}" required>
                        <button type="button" id="btn-edit-email" class="btn btn-sm" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: var(--primary); background: rgba(0,0,0,0.4); border-radius: 6px; font-size: 0.75rem; padding: 4px 8px;">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle;">edit</span> {{ translate('Change') }}
                        </button>
                    </div>

                    <!-- Step 2: OTP Input (Initially Hidden) -->
                    <div id="otp-input-group" style="display: none;">
                        <div class="form-group-premium mt-3">
                            <span class="material-icons input-icon">pin</span>
                            <input type="text" name="otp_code" id="otp_code" class="form-control input-premium"
                                placeholder="{{ translate('Enter 4-digit OTP') }}" maxlength="6"
                                style="letter-spacing: 4px; font-weight: 700; font-size: 1.1rem; text-align: center;">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                            <span id="timer-text" style="color: rgba(255, 255, 255, 0.6); font-size: 0.85rem;"></span>
                            <button type="button" id="btn-resend-otp" class="btn btn-link p-0" style="display: none; color: var(--primary); font-size: 0.85rem; text-decoration: none; font-weight: 600;">
                                {{ translate('Resend OTP') }}
                            </button>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center px-1 mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="otp_remember" value="1"
                                {{ request()->cookie('provider_remember_checked') ? 'checked' : '' }} id="otpRememberCheckbox">
                            <label class="form-check-label" for="otpRememberCheckbox">{{ translate('Keep me signed in') }}</label>
                        </div>
                        <a href="javascript:void(0)" id="switch-to-password" style="color: rgba(250, 210, 147, 0.8); font-size: 0.85rem; text-decoration: underline;">
                            {{ translate('Login with Password') }}
                        </a>
                    </div>

                    <!-- Action Buttons -->
                    <button type="button" class="btn btn-premium" id="btn-send-otp">{{ translate('Send Verification Code') }}</button>
                    <button type="button" class="btn btn-premium" id="btn-verify-otp" style="display: none;">{{ translate('Verify & Login to Terminal') }}</button>
                </form>
            </div>

            <!-- Password Login Form (Fallback / Alternative) -->
            <div id="password-login-container" style="display: none;">
                <form action="{{ route('provider.auth.login') }}" method="POST" id="login-form">
                    @csrf
                    <input type="hidden" name="login_type" id="pw_login_type" value="provider">

                    <div class="form-group-premium">
                        <span class="material-icons input-icon">email</span>
                        <input type="email" name="email_or_phone" class="form-control input-premium"
                            value="{{ request()->cookie('provider_remember_email') }}"
                            placeholder="{{ translate('Email') }}" required id="email">
                    </div>

                    <div class="form-group-premium">
                        <span class="material-icons input-icon">lock</span>
                        <input type="password" name="password" class="form-control input-premium"
                            value="{{ request()->cookie('provider_remember_password') }}"
                            placeholder="{{ translate('Password') }}" required id="password">
                        <span class="material-icons togglePassword" id="togglePassword">visibility_off</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center px-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" value="1"
                                {{ request()->cookie('provider_remember_checked') ? 'checked' : '' }} id="rememberMeCheckbox">
                            <label class="form-check-label" for="rememberMeCheckbox">{{ translate('Keep me signed in') }}</label>
                        </div>
                        <a href="{{ route('provider.auth.reset-password.index') }}" class="form-check-label"
                            style="color: var(--primary)">{{ translate('Forgot Password?') }}</a>
                    </div>

                    @php($recaptcha = business_config('recaptcha', 'third_party'))
                    @if (isset($recaptcha) && $recaptcha->is_active)
                        <div class="recaptcha d-flex justify-content-center mt-4">
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        </div>
                    @endif

                    <button type="submit" class="btn btn-premium" id="signInBtn">{{ translate('Login with Password') }}</button>

                    <div class="text-center mt-3">
                        <a href="javascript:void(0)" id="switch-to-otp" style="color: var(--primary); font-size: 0.85rem; text-decoration: underline;">
                            &larr; {{ translate('Back to Email OTP Login') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="form-footer mt-3">
                <span style="color: rgba(255, 255, 255, 0.65);">{{ translate('New Partner?') }}</span>
                <a href="{{ route('provider.auth.sign-up') }}" style="color: var(--primary); font-weight: 700; text-decoration: none; margin-left: 5px;">
                    {{ translate('Sign Up / Register Here') }} &rarr;
                </a>
            </div>
        </div>

        <div class="software-version">
            {{ translate('Version') }} {{ env('SOFTWARE_VERSION') }}
        </div>
    </div>

    <script src="{{ asset('public/assets/provider-module') }}/js/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('public/assets/provider-module') }}/js/toastr.js"></script>
    {!! Toastr::message() !!}

    <script>
        "use strict";

        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).text(type === 'password' ? 'visibility_off' : 'visibility');
        });

        @if (isset($recaptcha) && $recaptcha->is_active)
            $.getScript("https://www.google.com/recaptcha/api.js?render={{ $recaptcha->live_values['site_key'] }}",
                function() {
                    $('#signInBtn').click(function(e) {
                        e.preventDefault();
                        grecaptcha.ready(function() {
                            grecaptcha.execute('{{ $recaptcha->live_values['site_key'] }}', {
                                action: 'submit'
                            }).then(function(token) {
                                document.getElementById('g-recaptcha-response').value = token;
                                $('#login-form').submit();
                            });
                        });
                    });
                });
        @endif

        @if (env('APP_ENV') == 'demo')
            console.log("Demo Mode: provider@provider.com / 12345678");
        @endif

        @if (isset($errors) && $errors->any())
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}', 'Error', {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        @endif

        $('#btn-login-provider').on('click', function() {
            $(this).addClass('active-role-btn').css({'background': 'var(--gold-gradient)', 'color': '#000', 'font-weight': '700'});
            $('#btn-login-partner').removeClass('active-role-btn').css({'background': 'transparent', 'color': 'rgba(255, 255, 255, 0.65)', 'font-weight': '600'});
            $('#login_type').val('provider');
            $('#pw_login_type').val('provider');
            $('#login-title').text("{{ translate('Provider Sign In') }}");
            $('#signInBtn').text("{{ translate('Login to Terminal') }}");
        });

        $('#btn-login-partner').on('click', function() {
            $(this).addClass('active-role-btn').css({'background': 'var(--gold-gradient)', 'color': '#000', 'font-weight': '700'});
            $('#btn-login-provider').removeClass('active-role-btn').css({'background': 'transparent', 'color': 'rgba(255, 255, 255, 0.65)', 'font-weight': '600'});
            $('#login_type').val('partner');
            $('#pw_login_type').val('partner');
            $('#login-title').text("{{ translate('Partner Sign In') }}");
            $('#signInBtn').text("{{ translate('Login as Partner') }}");
        });

        // Switch between OTP and Password login modes
        $('#switch-to-password').on('click', function() {
            $('#otp-login-container').hide();
            $('#password-login-container').fadeIn(200);
            $('#email').val($('#otp_email').val());
        });

        $('#switch-to-otp').on('click', function() {
            $('#password-login-container').hide();
            $('#otp-login-container').fadeIn(200);
            $('#otp_email').val($('#email').val());
        });

        let countdownTimer = null;
        function startOtpTimer(seconds) {
            clearInterval(countdownTimer);
            let remaining = seconds;
            $('#btn-resend-otp').hide();
            $('#timer-text').show().text("{{ translate('Resend OTP in') }} " + remaining + "s");

            countdownTimer = setInterval(function() {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(countdownTimer);
                    $('#timer-text').hide();
                    $('#btn-resend-otp').fadeIn(200);
                } else {
                    $('#timer-text').text("{{ translate('Resend OTP in') }} " + remaining + "s");
                }
            }, 1000);
        }

        // Send OTP handler
        function sendOtpRequest() {
            const email = $('#otp_email').val().trim();
            if (!email) {
                toastr.error("{{ translate('Please enter your email address') }}");
                $('#otp_email').focus();
                return;
            }

            const sendBtn = $('#btn-send-otp');
            sendBtn.prop('disabled', true).text("{{ translate('Sending Verification Code...') }}");

            $.ajax({
                url: "{{ route('provider.auth.send-otp') }}",
                method: "POST",
                data: {
                    email: email,
                    login_type: $('#login_type').val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    toastr.success(res.message);
                    $('#otp_email').prop('readonly', true);
                    $('#btn-edit-email').show();
                    $('#otp-input-group').slideDown(250);
                    $('#btn-send-otp').hide();
                    $('#btn-verify-otp').show();
                    $('#otp_code').focus();
                    startOtpTimer(res.expires_in || 60);
                },
                error: function(err) {
                    const msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : "{{ translate('Failed to send verification code. Please try again.') }}";
                    toastr.error(msg);
                },
                complete: function() {
                    sendBtn.prop('disabled', false).text("{{ translate('Send Verification Code') }}");
                }
            });
        }

        $('#btn-send-otp').on('click', sendOtpRequest);
        $('#btn-resend-otp').on('click', sendOtpRequest);

        // Edit Email
        $('#btn-edit-email').on('click', function() {
            clearInterval(countdownTimer);
            $('#otp_email').prop('readonly', false).focus();
            $(this).hide();
            $('#otp-input-group').slideUp(200);
            $('#otp_code').val('');
            $('#btn-verify-otp').hide();
            $('#btn-send-otp').show();
            $('#timer-text').hide();
            $('#btn-resend-otp').hide();
        });

        // Verify OTP and Login
        $('#btn-verify-otp').on('click', function() {
            const email = $('#otp_email').val().trim();
            const otp = $('#otp_code').val().trim();

            if (!otp) {
                toastr.error("{{ translate('Please enter the verification code (OTP)') }}");
                $('#otp_code').focus();
                return;
            }

            const verifyBtn = $(this);
            verifyBtn.prop('disabled', true).text("{{ translate('Verifying & Logging In...') }}");

            $.ajax({
                url: "{{ route('provider.auth.otp-login') }}",
                method: "POST",
                data: {
                    email: email,
                    otp: otp,
                    login_type: $('#login_type').val(),
                    remember: $('#otpRememberCheckbox').is(':checked') ? 1 : 0,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    toastr.success(res.message);
                    setTimeout(function() {
                        window.location.href = res.redirect_url;
                    }, 500);
                },
                error: function(err) {
                    const msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : "{{ translate('Invalid OTP. Please try again.') }}";
                    toastr.error(msg);
                    verifyBtn.prop('disabled', false).text("{{ translate('Verify & Login to Terminal') }}");
                }
            });
        });

        // Allow pressing Enter key in inputs
        $('#otp_email').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                if ($('#btn-send-otp').is(':visible')) {
                    $('#btn-send-otp').trigger('click');
                }
            }
        });

        $('#otp_code').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                if ($('#btn-verify-otp').is(':visible')) {
                    $('#btn-verify-otp').trigger('click');
                }
            }
        });

        @if(request('type') == 'partner' || request('role') == 'partner')
            $('#btn-login-partner').trigger('click');
        @endif
    </script>
</body>

</html>
