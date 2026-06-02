<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { BriefcaseBusiness } from '@lucide/vue'

const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-background text-ink">
    <header class="border-b border-line bg-white">
      <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
        <Link href="/" class="flex items-center gap-3">
          <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white">
            <BriefcaseBusiness class="h-5 w-5" />
          </span>
          <span>
            <span class="block text-base font-semibold leading-5">Open Case</span>
            <span class="block text-xs text-muted">Not an official BIR website</span>
          </span>
        </Link>
        <div class="flex items-center gap-2">
          <Link href="/login" class="btn-secondary">Log in</Link>
          <Link href="/register" class="btn-primary">Start case</Link>
        </div>
      </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6">
      <div
        v-if="page.props.flash?.success || page.props.flash?.error || page.props.flash?.status"
        class="mb-4 rounded-lg border px-4 py-3 text-sm"
        :class="page.props.flash?.error ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800'"
      >
        {{ page.props.flash?.error || page.props.flash?.success || page.props.flash?.status }}
      </div>
      <slot />
    </main>
  </div>
</template>
