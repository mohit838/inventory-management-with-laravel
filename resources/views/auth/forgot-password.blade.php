<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>
    <x-slot name="heading">Reset your password</x-slot>

    <div class="text-sm text-gray-700 mb-6 leading-relaxed">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <form class="space-y-6" method="POST" action="{{ route('password.request') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
                <input id="email" name="email" type="email" autocomplete="email" required 
                       value="{{ old('email') }}"
                       class="block w-full rounded-lg border-0 px-4 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition-all @error('email') ring-red-500 @enderror">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit" 
                    class="flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold leading-6 text-white shadow-lg hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all hover:shadow-xl hover:-translate-y-0.5">
                Email password reset link
            </button>
        </div>
    </form>

    <p class="mt-10 text-center text-sm text-gray-600">
        Remember your password?
        <a href="{{ route('login') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500 transition-colors">Sign in</a>
    </p>
</x-guest-layout>
