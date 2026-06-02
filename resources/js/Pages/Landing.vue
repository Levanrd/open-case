<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, CheckCircle2, FileText, ShieldCheck } from '@lucide/vue'

defineOptions({ layout: GuestLayout })

const steps = [
  'Create a business owner account',
  'Submit business and tax profile details',
  'Review preliminary risk and document checklist',
  'Choose an Open Case service package',
]
</script>

<template>
  <Head title="Tax readiness workspace" />

  <section class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
    <div>
      <p class="text-sm font-semibold uppercase tracking-wide text-primary">Philippine business closure and open-case readiness</p>
      <h1 class="mt-3 max-w-3xl text-4xl font-semibold leading-tight text-ink sm:text-5xl">
        Open Case
      </h1>
      <p class="mt-4 max-w-2xl text-lg leading-8 text-muted">
        Understand possible BIR open cases, closure requirements, missing tax documents, and service-review next steps before working with an internal accountant.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <Link href="/register" class="btn-primary">
          Start case
          <ArrowRight class="h-4 w-4" />
        </Link>
        <Link href="/login" class="btn-secondary">Log in</Link>
      </div>
    </div>

    <div class="surface p-5">
      <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-primary">
          <ShieldCheck class="h-5 w-5" />
        </span>
        <div>
          <p class="font-semibold">Controlled review model</p>
          <p class="text-sm text-muted">Accountants are created by admin only.</p>
        </div>
      </div>
      <div class="mt-5 space-y-3">
        <div v-for="step in steps" :key="step" class="flex gap-3 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
          <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-primary" />
          <span class="text-sm">{{ step }}</span>
        </div>
      </div>
      <div class="mt-5 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
        <FileText class="mt-0.5 h-4 w-4 shrink-0" />
        <p>Open Case is not connected to BIR and does not collect government taxes, penalties, or filing fees.</p>
      </div>
    </div>
  </section>
</template>
