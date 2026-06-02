<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { CheckCircle2, Download, UserRoundCheck } from '@lucide/vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  caseRecord: { type: Object, required: true },
  accountants: { type: Array, default: () => [] },
})

const assignForm = useForm({
  assigned_accountant_id: props.caseRecord.assigned_accountant_id || '',
})

function assign() {
  assignForm.patch(`/admin/cases/${props.caseRecord.id}/assign`)
}

function verify(paymentId) {
  useForm({}).patch(`/admin/payments/${paymentId}/verify`)
}

function money(value) {
  return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0))
}
</script>

<template>
  <Head :title="`Admin ${caseRecord.case_number}`" />
  <div class="space-y-5">
    <section class="surface p-4 sm:p-5">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold">{{ caseRecord.case_number }}</h1>
          <p class="mt-1 text-sm text-muted">{{ caseRecord.user?.name }} · {{ caseRecord.business?.registered_name }}</p>
        </div>
        <Link href="/admin/cases" class="btn-secondary">Back to cases</Link>
      </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1fr_360px]">
      <div class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title">Preliminary result</h2>
          <p class="mt-2 text-sm leading-6 text-muted">{{ caseRecord.result?.generated_summary }}</p>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Risk score</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.result?.risk_score }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Risk level</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.risk_level }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Status</p>
              <p class="mt-1 text-sm font-semibold capitalize">{{ String(caseRecord.status).replaceAll('_', ' ') }}</p>
            </div>
          </div>
        </div>

        <div class="surface overflow-hidden">
          <div class="border-b border-line p-4">
            <h2 class="section-title">Documents</h2>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>Type</th>
                <th>File</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="document in caseRecord.documents" :key="document.id">
                <td>{{ document.document_type }}</td>
                <td>{{ document.original_filename || 'Requested' }}</td>
                <td><span class="badge-neutral">{{ document.status }}</span></td>
                <td class="text-right">
                  <a v-if="document.file_path" :href="`/documents/${document.id}/download`" class="btn-secondary">
                    <Download class="h-4 w-4" />
                    Download
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <aside class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title">Assign accountant</h2>
          <form class="mt-4 space-y-3" @submit.prevent="assign">
            <select v-model="assignForm.assigned_accountant_id" class="input" required>
              <option value="">Select accountant</option>
              <option v-for="accountant in accountants" :key="accountant.id" :value="accountant.id">{{ accountant.name }}</option>
            </select>
            <button class="btn-primary w-full" type="submit" :disabled="assignForm.processing">
              <UserRoundCheck class="h-4 w-4" />
              Assign
            </button>
          </form>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Payments</h2>
          <div class="mt-3 space-y-3">
            <div v-for="payment in caseRecord.payments" :key="payment.id" class="rounded-lg border border-line p-3">
              <p class="font-semibold">{{ payment.service_package?.name }}</p>
              <p class="text-sm text-muted">{{ money(payment.amount) }} · {{ payment.provider_reference || 'No reference' }}</p>
              <p class="mt-1 text-sm capitalize">{{ String(payment.status).replaceAll('_', ' ') }}</p>
              <button v-if="payment.status === 'pending_review'" class="btn-primary mt-3 w-full" type="button" @click="verify(payment.id)">
                <CheckCircle2 class="h-4 w-4" />
                Verify payment
              </button>
            </div>
          </div>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Audit trail</h2>
          <div class="mt-3 space-y-2">
            <div v-for="log in caseRecord.audit_logs" :key="log.id" class="rounded-lg bg-slate-50 p-3">
              <p class="text-sm font-medium">{{ log.action }}</p>
              <p class="text-xs text-muted">{{ log.user?.name || 'System' }} · {{ new Date(log.created_at).toLocaleString() }}</p>
            </div>
          </div>
        </div>
      </aside>
    </section>
  </div>
</template>
