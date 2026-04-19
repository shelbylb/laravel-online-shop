<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (! auth()->user()->hasVerifiedEmail())
                <div class="p-4 sm:p-6 bg-yellow-50 border border-yellow-200 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                            Email не подтверждён
                        </h3>

                        <p class="text-sm text-yellow-700 mb-4">
                            Подтвердите ваш email, чтобы получить доступ к оформлению заказов.
                            Мы отправили письмо со ссылкой для подтверждения на адрес
                            <strong>{{ auth()->user()->email }}</strong>.
                        </p>

                        @if (session('status'))
                            <div class="mb-4 text-sm font-medium text-green-600">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf

                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition"
                            >
                                Отправить ещё раз
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
