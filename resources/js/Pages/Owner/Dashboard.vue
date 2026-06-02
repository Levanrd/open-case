<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, ClipboardCheck, CreditCard, FilePlus2, FolderOpen } from '@lucide/vue'

defineOptions({ layout: AppLayout })

defineProps({
  cases: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
  packages: { type: Array, default: () => [] },
})

const statusLabels = {
  submitted: 'Submitted',
  awaiting_payment: 'Awaiting payment',
  payment_review: 'Payment review',
  paid: 'Paid',
  accountant_assigned: 'Accountant assigned',
  under_review: 'Under review',
  needs_more_documents: 'Needs documents',
  ready_for_bir_processing: 'Ready for BIR',
  completed: 'Completed',
}

function badgeClass(status) {
  if (['completed', 'paid'].includes(status)) return 'badge-success'
  if (['awaiting_payment', 'payment_review', 'needs_more_documents'].includes(status)) return 'badge-warning'
  if (['under_review', 'accountant_assigned', 'ready_for_bir_processing'].includes(status)) return 'badge-info'
  return 'badge-neutral'
}

function money(value) {
  return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0))
}
</script>

<template>
  <Head title="Owner Dashboard" />

  <div class="space-y-5">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Owner dashboard</h1>
        <p class="mt-1 text-sm text-muted">Track case status, uploaded documents, review notes, and payments.</p>
      </div>
      <Link href="/cases/new" class="btn-primary">
        <FilePlus2 class="h-4 w-4" />
        Start case
      </Link>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <div class="surface p-4">
        <p class="text-sm text-muted">Total cases</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.total || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Open</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.open || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Needs payment</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.needs_payment || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Completed</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.completed || 0 }}</p>
      </div>
    </section>

    <section class="surface overflow-hidden">
      <div class="flex items-center justify-between border-b border-line p-4">
        <div>
          <h2 class="section-title">Cases</h2>
          <p class="muted">Your Open Case reviews and current status.</p>
        </div>
      </div>

      <div v-if="cases.length" class="overflow-x-auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>Case</th>
              <th>Business</th>
              <th>Risk</th>
              <th>Payment</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="caseRecord in cases" :key="caseRecord.id">
              <td>
                <p class="font-semibold">{{ caseRecord.case_number }}</p>
                <p class="text-xs capitalize text-muted">{{ String(caseRecord.case_type).replaceAll('_', ' ') }}</p>
              </td>
              <td>
                <p class="font-medium">{{ caseRecord.business?.registered_name }}</p>
                <p class="text-xs text-muted">{{ caseRecord.business?.masked_tin || 'TIN masked after upload' }}</p>
              </td>
              <td>
                <span :class="caseRecord.risk_level === 'High' ? 'badge-danger' : caseRecord.risk_level === 'Elevated' ? 'badge-warning' : 'badge-neutral'">
                  {{ caseRecord.risk_level || 'Pending' }}
                </span>
              </td>
              <td>
                <p class="font-medium">{{ caseRecord.latest_payment?.service_package?.name || 'No package' }}</p>
                <p class="text-xs text-muted">{{ caseRecord.latest_payment?.status || 'Not selected' }}</p>
              </td>
              <td><span :class="badgeClass(caseRecord.status)">{{ statusLabels[caseRecord.status] || caseRecord.status }}</span></td>
              <td class="text-right">
                <Link :href="`/cases/${caseRecord.id}`" class="btn-secondary">
                  Open
                  <ArrowRight class="h-4 w-4" />
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="grid gap-4 p-6 sm:grid-cols-[1fr_auto] sm:items-center">
        <div>
          <div class="flex items-center gap-2 text-lg font-semibold">
            <FolderOpen class="h-5 w-5 text-primary" />
            No cases yet
          </div>
          <p class="mt-1 text-sm text-muted">Create your first case to generate a preliminary result and checklist.</p>
        </div>
        <Link href="/cases/new" class="btn-primary">Start case</Link>
      </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-[1fr_1fr]">
      <div class="surface p-4">
        <h2 class="section-title flex items-center gap-2">
          <ClipboardCheck class="h-5 w-5 text-primary" />
          Review boundaries
        </h2>
        <div class="mt-3 space-y-2 text-sm text-muted">
          <p>Results are preliminary and based only on information you submit.</p>
          <p>Open Case service fees are separate from BIR taxes, penalties, and government fees.</p>
        </div>
      </div>
      <div class="surface p-4">
        <h2 class="section-title flex items-center gap-2">
          <CreditCard class="h-5 w-5 text-primary" />
          Available packages
        </h2>
        <div class="mt-3 space-y-3">
          <div v-for="pkg in packages.slice(0, 3)" :key="pkg.id" class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
            <div>
              <p class="font-medium">{{ pkg.name }}</p>
              <p class="text-xs text-muted">{{ pkg.description }}</p>
            </div>
            <p class="shrink-0 font-semibold">{{ money(pkg.price) }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
