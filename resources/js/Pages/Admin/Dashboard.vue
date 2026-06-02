<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { CheckCircle2, Plus, UserRoundCheck } from '@lucide/vue'

defineOptions({ layout: AdminLayout })

defineProps({
  stats: { type: Object, default: () => ({}) },
  cases: { type: Array, default: () => [] },
  pendingPayments: { type: Array, default: () => [] },
  accountants: { type: Array, default: () => [] },
  packages: { type: Array, default: () => [] },
})

const packageForm = useForm({
  name: '',
  description: '',
  price: '',
  case_type: '',
  features: [],
  active: true,
  sort_order: 50,
})

function verify(paymentId) {
  useForm({}).patch(`/admin/payments/${paymentId}/verify`)
}

function submitPackage() {
  packageForm.post('/admin/packages', {
    onSuccess: () => packageForm.reset(),
  })
}

function money(value) {
  return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0))
}

function statusLabel(status) {
  return String(status || '').replaceAll('_', ' ')
}
</script>

<template>
  <Head title="Admin" />

  <div class="space-y-5">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Admin dashboard</h1>
        <p class="mt-1 text-sm text-muted">Monitor payments, case assignment, packages, and operations.</p>
      </div>
      <Link href="/admin/accountants/create" class="btn-primary">
        <UserRoundCheck class="h-4 w-4" />
        New accountant
      </Link>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
      <div class="surface p-4">
        <p class="text-sm text-muted">Total cases</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.total_cases || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Pending payments</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.pending_payments || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Unassigned paid</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.unassigned_paid || 0 }}</p>
      </div>
      <div class="surface p-4">
        <p class="text-sm text-muted">Accountants</p>
        <p class="mt-2 text-3xl font-semibold">{{ stats.active_accountants || 0 }}</p>
      </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1fr_360px]">
      <div class="space-y-5">
        <div class="surface overflow-hidden">
          <div class="border-b border-line p-4">
            <h2 class="section-title">Payment verification</h2>
            <p class="muted">Manual Open Case service-fee references awaiting admin review.</p>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Case</th>
                  <th>Client</th>
                  <th>Package</th>
                  <th>Reference</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="payment in pendingPayments" :key="payment.id">
                  <td>
                    <Link :href="`/admin/cases/${payment.tax_case?.id}`" class="font-semibold text-primary">{{ payment.tax_case?.case_number }}</Link>
                    <p class="text-xs text-muted">{{ payment.tax_case?.business?.registered_name }}</p>
                  </td>
                  <td>{{ payment.tax_case?.user?.name }}</td>
                  <td>
                    <p>{{ payment.service_package?.name }}</p>
                    <p class="text-xs text-muted">{{ money(payment.amount) }}</p>
                  </td>
                  <td>{{ payment.provider_reference }}</td>
                  <td class="text-right">
                    <button class="btn-primary" type="button" @click="verify(payment.id)">
                      <CheckCircle2 class="h-4 w-4" />
                      Verify
                    </button>
                  </td>
                </tr>
                <tr v-if="!pendingPayments.length">
                  <td colspan="5" class="text-muted">No payment references pending verification.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="surface overflow-hidden">
          <div class="flex items-center justify-between border-b border-line p-4">
            <div>
              <h2 class="section-title">Recent cases</h2>
              <p class="muted">Newest case submissions and assignment state.</p>
            </div>
            <Link href="/admin/cases" class="btn-secondary">View all</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Case</th>
                  <th>Business</th>
                  <th>Risk</th>
                  <th>Assigned</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="caseRecord in cases" :key="caseRecord.id">
                  <td><Link :href="`/admin/cases/${caseRecord.id}`" class="font-semibold text-primary">{{ caseRecord.case_number }}</Link></td>
                  <td>{{ caseRecord.business?.registered_name }}</td>
                  <td>{{ caseRecord.risk_level || 'Pending' }}</td>
                  <td>{{ caseRecord.assigned_accountant?.name || 'Unassigned' }}</td>
                  <td><span class="badge-neutral capitalize">{{ statusLabel(caseRecord.status) }}</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <aside class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title">Service packages</h2>
          <form class="mt-4 space-y-3" @submit.prevent="submitPackage">
            <input v-model="packageForm.name" class="input" placeholder="Package name" required />
            <textarea v-model="packageForm.description" class="input min-h-20" placeholder="Description" required></textarea>
            <input v-model="packageForm.price" class="input" type="number" min="0" step="0.01" placeholder="Price" required />
            <select v-model="packageForm.case_type" class="input">
              <option value="">Any case type</option>
              <option value="open_case_review">Open case review</option>
              <option value="business_closure">Business closure</option>
              <option value="compliance_cleanup">Compliance cleanup</option>
            </select>
            <button class="btn-primary w-full" type="submit" :disabled="packageForm.processing">
              <Plus class="h-4 w-4" />
              Add package
            </button>
          </form>

          <div class="mt-4 space-y-3">
            <div v-for="pkg in packages" :key="pkg.id" class="rounded-lg border border-line p-3">
              <div class="flex items-start justify-between gap-3">
                <p class="font-semibold">{{ pkg.name }}</p>
                <span :class="pkg.active ? 'badge-success' : 'badge-neutral'">{{ pkg.active ? 'Active' : 'Paused' }}</span>
              </div>
              <p class="mt-1 text-sm text-muted">{{ money(pkg.price) }}</p>
            </div>
          </div>
        </div>
      </aside>
    </section>
  </div>
</template>
