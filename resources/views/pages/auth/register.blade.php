<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf


            <div
                x-data="{
                    role: @js(old('role', 'candidat')),
                    nameLabel: @js(__('Name')),
                    companyLabel: @js(__('Company\'s name')),
                    namePlaceholder: @js(__('Full name')),
                }"
                class="flex flex-col gap-6"
            >

                <!-- Role Choice -->
                <flux:radio.group name="role" :label="__('Role')" required x-model="role">
                    <flux:radio value="candidat" :label="__('Candidat')" />
                    <flux:radio value="company" :label="__('Company')" />
                </flux:radio.group>

                <!-- Name -->
                <flux:field>
                    <flux:label x-text="role === 'company' ? companyLabel : nameLabel">
                        {{ __('Name') }}
                    </flux:label>

                    <flux:input
                        name="name"
                        :value="old('name')"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        x-bind:placeholder="role === 'company' ? companyLabel : namePlaceholder"
                    />

                    <flux:error name="name" />
                </flux:field>

            </div>

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            


            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
