<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Спасибо за регистрацию! Перед тем как продолжить, пожалуйста, подтвердите email,
        перейдя по ссылке, которую мы отправили вам на почту.

        Если письмо не пришло, нажмите кнопку ниже, и мы отправим его повторно.
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Отправить письмо ещё раз
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none">
                Выйти
            </button>
        </form>
    </div>
</x-guest-layout>
