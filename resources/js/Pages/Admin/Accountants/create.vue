<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { UserPlus } from '@lucide/vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/admin/accountants')
}
</script>

<template>
  <Head title="New Accountant" />
  <section class="surface mx-auto max-w-2xl p-5">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold">New accountant</h1>
        <p class="mt-1 text-sm text-muted">Accountants cannot register publicly.</p>
      </div>
      <Link href="/admin/accountants" class="btn-secondary">Back</Link>
    </div>
    <form class="mt-6 grid gap-4 sm:grid-cols-2" @submit.prevent="submit">
      <div>
        <label class="label" for="first_name">First name</label>
        <input id="first_name" v-model="form.first_name" class="input mt-1" required />
        <p v-if="form.errors.first_name" class="error">{{ form.errors.first_name }}</p>
      </div>
      <div>
        <label class="label" for="last_name">Last name</label>
        <input id="last_name" v-model="form.last_name" class="input mt-1" required />
        <p v-if="form.errors.last_name" class="error">{{ form.errors.last_name }}</p>
      </div>
      <div class="sm:col-span-2">
        <label class="label" for="email">Email</label>
        <input id="email" v-model="form.email" class="input mt-1" type="email" required />
        <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
      </div>
      <div class="sm:col-span-2">
        <label class="label" for="phone">Phone</label>
        <input id="phone" v-model="form.phone" class="input mt-1" />
      </div>
      <div>
        <label class="label" for="password">Temporary password</label>
        <input id="password" v-model="form.password" class="input mt-1" type="password" required />
        <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
      </div>
      <div>
        <label class="label" for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" v-model="form.password_confirmation" class="input mt-1" type="password" required />
      </div>
      <div class="sm:col-span-2">
        <button class="btn-primary" type="submit" :disabled="form.processing">
          <UserPlus class="h-4 w-4" />
          Create accountant
        </button>
      </div>
    </form>
  </section>
</template>
