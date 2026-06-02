<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import GoogleLoginButton from '@/components/google-login-button/GoogleLoginButton.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { LogIn } from '@lucide/vue'

defineOptions({ layout: GuestLayout })

const form = useForm({
  email: '',
  password: '',
  remember: true,
})

function submit() {
  form.post('/login')
}
</script>

<template>
  <Head title="Log in" />

  <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_420px]">
    <section class="py-6">
      <p class="text-sm font-semibold uppercase tracking-wide text-primary">Owner, admin, and accountant access</p>
      <h1 class="mt-3 max-w-2xl text-4xl font-semibold leading-tight text-ink">
        Continue your BIR readiness review.
      </h1>
      <p class="mt-4 max-w-2xl text-base leading-7 text-muted">
        Open Case keeps client documents, preliminary results, accountant notes, payment status, and case progress in one controlled workspace.
      </p>
      <div class="mt-6 grid max-w-2xl gap-3 sm:grid-cols-3">
        <div class="surface p-4">
          <p class="text-sm font-semibold">Private files</p>
          <p class="mt-1 text-sm text-muted">Uploads stay in private storage.</p>
        </div>
        <div class="surface p-4">
          <p class="text-sm font-semibold">Role access</p>
          <p class="mt-1 text-sm text-muted">Accountants see assigned cases only.</p>
        </div>
        <div class="surface p-4">
          <p class="text-sm font-semibold">Audit trail</p>
          <p class="mt-1 text-sm text-muted">Admin and review actions are logged.</p>
        </div>
      </div>
    </section>

    <section class="surface p-5">
      <h2 class="text-xl font-semibold">Log in</h2>
      <p class="mt-1 text-sm text-muted">Use your approved Open Case account.</p>

      <form class="mt-5 space-y-4" @submit.prevent="submit">
        <div>
          <label class="label" for="email">Email</label>
          <input id="email" v-model="form.email" class="input mt-1" type="email" autocomplete="email" required />
          <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
        </div>
        <div>
          <label class="label" for="password">Password</label>
          <input id="password" v-model="form.password" class="input mt-1" type="password" autocomplete="current-password" required />
          <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
        </div>
        <label class="flex items-center gap-2 text-sm text-muted">
          <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-line text-primary" />
          Remember this device
        </label>

        <button class="btn-primary w-full" type="submit" :disabled="form.processing">
          <LogIn class="h-4 w-4" />
          Log in
        </button>
      </form>

      <div class="my-5 flex items-center gap-3 text-xs uppercase text-muted">
        <span class="h-px flex-1 bg-line"></span>
        or
        <span class="h-px flex-1 bg-line"></span>
      </div>

      <GoogleLoginButton />

      <div class="mt-5 flex items-center justify-between text-sm">
        <Link href="/forgot-password" class="font-medium text-primary">Reset password</Link>
        <Link href="/register" class="font-medium text-primary">Create owner account</Link>
      </div>
    </section>
  </div>
</template>
