<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import {
  BriefcaseBusiness,
  ClipboardCheck,
  FileText,
  LayoutDashboard,
  LogOut,
  Plus,
  ShieldCheck,
  Users,
} from '@lucide/vue'

const page = usePage()
const user = page.props.auth?.user

const navByRole = {
  business_owner: [
    { label: 'Dashboard', href: '/owner/dashboard', icon: LayoutDashboard },
    { label: 'Start Case', href: '/cases/new', icon: Plus },
  ],
  accountant: [{ label: 'Assigned Cases', href: '/accountant/cases', icon: ClipboardCheck }],
  admin: [
    { label: 'Admin', href: '/admin', icon: ShieldCheck },
    { label: 'Cases', href: '/admin/cases', icon: FileText },
    { label: 'Accountants', href: '/admin/accountants', icon: Users },
  ],
}

const navItems = navByRole[user?.role] ?? navByRole.business_owner

function logout() {
  router.post('/logout')
}
</script>

<template>
  <div class="app-shell">
    <header class="border-b border-line bg-white">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <Link href="/dashboard" class="flex min-w-0 items-center gap-3">
          <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary text-white">
            <BriefcaseBusiness class="h-5 w-5" />
          </span>
          <span class="min-w-0">
            <span class="block text-base font-semibold leading-5 text-ink">Open Case</span>
            <span class="block truncate text-xs text-muted">Tax readiness workspace</span>
          </span>
        </Link>

        <div class="flex items-center gap-2">
          <span class="hidden text-right text-sm sm:block">
            <span class="block font-medium text-ink">{{ user?.display_name || user?.name || user?.email }}</span>
            <span class="block text-xs capitalize text-muted">{{ String(user?.role || '').replace('_', ' ') }}</span>
          </span>
          <button type="button" class="btn-secondary" title="Log out" @click="logout">
            <LogOut class="h-4 w-4" />
            <span class="hidden sm:inline">Log out</span>
          </button>
        </div>
      </div>
    </header>

    <div class="mx-auto grid max-w-7xl gap-5 px-4 py-5 sm:px-6 lg:grid-cols-[220px_1fr]">
      <aside class="lg:sticky lg:top-5 lg:self-start">
        <nav class="surface p-2">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-slate-50 hover:text-ink"
            :class="{ 'bg-teal-50 text-primary': page.url.startsWith(item.href) }"
          >
            <component :is="item.icon" class="h-4 w-4" />
            {{ item.label }}
          </Link>
        </nav>
      </aside>

      <main class="min-w-0">
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
  </div>
</template>
