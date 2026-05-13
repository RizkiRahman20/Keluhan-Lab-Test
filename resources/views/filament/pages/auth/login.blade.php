<div>

    @vite('resources/css/app.css')

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy: #1e2d5a;
            --navy-dark: #152040;
            --navy-mid: #2a3f7e;
            --accent: #3d5fc4;
            --panel-bg: #c8cfe8;
            --white: #ffffff;
            --input-bg: #ffffff;
            --input-border: #d0d7ea;
            --text: #1a2340;
            --error: #dc2626;
        }

        .login-outer {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(61,95,196,0.1) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(30,45,90,0.08) 0%, transparent 50%),
                #e8ecf5;
        }

        .login-card {
            display: flex;
            width: 100%;
            max-width: 860px;
            min-height: 460px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 24px 60px rgba(30,45,90,0.18),
                0 4px 16px rgba(30,45,90,0.08);
        }

        .login-photo {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: var(--navy-dark);
            min-height: 460px;
        }

        .login-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .login-photo-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(21,32,64,0.35) 0%,
                rgba(30,45,90,0.15) 100%
            );
        }

        .login-photo-badge {
            position: absolute;
            bottom: 24px;
            left: 24px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
            padding: 10px 16px;
        }

        .login-photo-badge p {
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .login-photo-badge span {
            color: rgba(255,255,255,0.7);
            font-size: 11px;
        }

        .login-form-panel {
            width: 380px;
            background: var(--panel-bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
        }

        .login-logo {
            width: 72px;
            height: 72px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(30,45,90,0.15);
        }

        .login-logo img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }

        .login-heading {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-heading h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--navy-dark);
            margin-bottom: 4px;
        }

        .login-heading p {
            font-size: 13px;
            color: rgba(26,35,64,0.6);
        }

        .login-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--input-border);
            border-radius: 10px;
            padding: 13px 16px 13px 44px;
            font-size: 14px;
            color: var(--text);
            outline: none;
        }

        .input-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(61,95,196,0.15);
        }

        .input-group input.is-error {
            border-color: var(--error);
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aaac4;
        }

        .input-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9aaac4;
        }

        .error-msg {
            font-size: 11px;
            color: var(--error);
            margin-top: 6px;
            padding-left: 4px;
        }

        .alert-error {
            background: rgba(220,38,38,0.1);
            border: 1px solid rgba(220,38,38,0.25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: #b91c1c;
        }

        .login-btn {
            width: 100%;
            background: var(--navy);
            color: white;
            font-weight: 700;
            font-size: 15px;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 4px;
        }

        .login-btn:hover {
            background: var(--navy-mid);
        }

        @media (max-width: 640px) {
            .login-photo {
                display: none;
            }

            .login-card {
                max-width: 400px;
            }

            .login-form-panel {
                width: 100%;
                padding: 40px 28px;
            }
        }
    </style>

    <div class="login-outer">

        <div class="login-card">

            {{-- PANEL FOTO --}}
            <div class="login-photo">

                <img
                    src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80"
                    alt="Foto Laboratorium"
                >

                <div class="login-photo-overlay"></div>

                <div class="login-photo-badge">
                    <p>Lab ICT Terpadu</p>
                    <span>Sistem Pelaporan Keluhan</span>
                </div>

            </div>

            {{-- PANEL LOGIN --}}
            <div class="login-form-panel">

                {{-- LOGO --}}
                <div class="login-logo">

                    <img
                        src="{{ asset('images/logoict.jpg') }}"
                        alt="Logo"
                    >

                </div>

                {{-- HEADING --}}
                <div class="login-heading">
                    <h1>Hello Again!</h1>
                    <p>Selamat datang di Lab ICT</p>
                </div>

                {{-- FORM --}}
                <div class="login-form">

                    {{-- ERROR --}}
                    @if ($errors->has('email') || session('status'))

                        <div class="alert-error">
                            {{ $errors->first('email') ?? session('status') }}
                        </div>

                    @endif

                    {{-- EMAIL --}}
                    <div>

                        <div class="input-group">

                            <svg class="input-icon" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0z"/>

                            </svg>

                            <input
                                type="email"
                                wire:model="data.email"
                                placeholder="Email"
                                autocomplete="email"
                                class="{{ $errors->has('email') ? 'is-error' : '' }}"
                            >

                        </div>

                        @error('email')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror

                    </div>

                    {{-- PASSWORD --}}
                    <div>

                        <div class="input-group" x-data="{ show: false }">

                            <svg class="input-icon" width="16" height="16" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>

                            </svg>

                            <input
                                :type="show ? 'text' : 'password'"
                                wire:model="data.password"
                                placeholder="Password"
                                autocomplete="current-password"
                            >

                            <button
                                type="button"
                                class="input-toggle"
                                @click="show = !show"
                            >
                                👁
                            </button>

                        </div>

                    </div>

                    {{-- BUTTON --}}
                    <button
                        type="button"
                        class="login-btn"
                        wire:click="authenticate"
                    >
                        Login
                    </button>

                </div>

            </div>

        </div>

    </div>

    @vite('resources/js/app.js')

</div>