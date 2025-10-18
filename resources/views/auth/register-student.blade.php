<x-guest-layout>
    <x-slot name="title">Register as Student Provider</x-slot>
    <x-slot name="heading">Join as a Student Provider</x-slot>
    <x-slot name="subheading">Offer your skills and earn money while studying</x-slot>

    <form method="POST" action="{{ route('register.student') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('University Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Use your university email address for verification</p>
        </div>

        <!-- University -->
        <div class="mt-4">
            <x-input-label for="university" :value="__('University')" />
            <select id="university" name="university" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                <option value="">Select your university</option>
                <option value="Addis Ababa University" {{ old('university') == 'Addis Ababa University' ? 'selected' : '' }}>Addis Ababa University</option>
                <option value="Bahir Dar University" {{ old('university') == 'Bahir Dar University' ? 'selected' : '' }}>Bahir Dar University</option>
                <option value="Jimma University" {{ old('university') == 'Jimma University' ? 'selected' : '' }}>Jimma University</option>
                <option value="Hawassa University" {{ old('university') == 'Hawassa University' ? 'selected' : '' }}>Hawassa University</option>
                <option value="Mekelle University" {{ old('university') == 'Mekelle University' ? 'selected' : '' }}>Mekelle University</option>
                <option value="Gondar University" {{ old('university') == 'Gondar University' ? 'selected' : '' }}>Gondar University</option>
                <option value="Haramaya University" {{ old('university') == 'Haramaya University' ? 'selected' : '' }}>Haramaya University</option>
                <option value="Arba Minch University" {{ old('university') == 'Arba Minch University' ? 'selected' : '' }}>Arba Minch University</option>
                <option value="Other" {{ old('university') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('university')" class="mt-2" />
        </div>

        <!-- Field of Study -->
        <div class="mt-4">
            <x-input-label for="field_of_study" :value="__('Field of Study')" />
            <x-text-input id="field_of_study" class="block mt-1 w-full" type="text" name="field_of_study" :value="old('field_of_study')" required placeholder="e.g., Computer Science, Graphic Design" />
            <x-input-error :messages="$errors->get('field_of_study')" class="mt-2" />
        </div>

        <!-- Year of Study -->
        <div class="mt-4">
            <x-input-label for="year_of_study" :value="__('Year of Study')" />
            <select id="year_of_study" name="year_of_study" class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                <option value="">Select year</option>
                <option value="1" {{ old('year_of_study') == '1' ? 'selected' : '' }}>1st Year</option>
                <option value="2" {{ old('year_of_study') == '2' ? 'selected' : '' }}>2nd Year</option>
                <option value="3" {{ old('year_of_study') == '3' ? 'selected' : '' }}>3rd Year</option>
                <option value="4" {{ old('year_of_study') == '4' ? 'selected' : '' }}>4th Year</option>
                <option value="5" {{ old('year_of_study') == '5' ? 'selected' : '' }}>5th Year</option>
                <option value="graduate" {{ old('year_of_study') == 'graduate' ? 'selected' : '' }}>Graduate Student</option>
            </select>
            <x-input-error :messages="$errors->get('year_of_study')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms and Conditions -->
        <div class="mt-4">
            <label for="terms" class="inline-flex items-center">
                <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                <span class="ms-2 text-sm text-gray-600">
                    I agree to the <a href="#" class="underline text-blue-600 hover:text-blue-800">Terms of Service</a> and <a href="#" class="underline text-blue-600 hover:text-blue-800">Privacy Policy</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register as Student') }}
            </x-primary-button>
        </div>
    </form>

    <x-slot name="footer">
        <p>Looking to hire talent? <a href="{{ route('register.client') }}" class="font-medium text-blue-600 hover:text-blue-800">Register as a Client</a></p>
    </x-slot>
</x-guest-layout>
