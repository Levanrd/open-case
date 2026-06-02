<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { CreditCard, Download, FileUp, Send, ShieldAlert } from '@lucide/vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  caseRecord: { type: Object, required: true },
  packages: { type: Array, default: () => [] },
  clientNotes: { type: Array, default: () => [] },
})

const selectedPackageId = ref('')

const uploadForm = useForm({
  document_type: 'Supporting document',
  document: null,
})

const paymentForm = useForm({
  service_package_id: '',
})

const referenceForm = useForm({
  provider_reference: '',
})

const fulfillForms = ref({})

const requestedDocuments = computed(() => props.caseRecord.documents?.filter((document) => document.status === 'requested') || [])
const uploadedDocuments = computed(() => props.caseRecord.documents?.filter((document) => document.status !== 'requested') || [])
const payment = computed(() => props.caseRecord.latest_payment)

const statusLabels = {
  submitted: 'Submitted',
  awaiting_payment: 'Awaiting payment',
  payment_review: 'Payment review',
  paid: 'Paid',
  accountant_assigned: 'Accountant assigned',
  under_review: 'Under review',
  needs_more_documents: 'Needs documents',
  ready_for_bir_processing: 'Ready for BIR processing',
  completed: 'Completed',
}

function money(value) {
  return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(Number(value || 0))
}

function badgeClass(status) {
  if (['completed', 'paid'].includes(status)) return 'badge-success'
  if (['awaiting_payment', 'payment_review', 'needs_more_documents'].includes(status)) return 'badge-warning'
  if (['under_review', 'accountant_assigned', 'ready_for_bir_processing'].includes(status)) return 'badge-info'
  return 'badge-neutral'
}

function selectPackage(packageId) {
  paymentForm.service_package_id = packageId
  selectedPackageId.value = packageId
  paymentForm.post(`/cases/${props.caseRecord.id}/package`)
}

function submitReference() {
  referenceForm.post(`/cases/${props.caseRecord.id}/payment-reference`)
}

function uploadDocument() {
  uploadForm.post(`/cases/${props.caseRecord.id}/documents`, {
    forceFormData: true,
    onSuccess: () => uploadForm.reset('document'),
  })
}

function fulfill(documentId, event) {
  const form = useForm({ document: event.target.files?.[0] || null })
  fulfillForms.value[documentId] = form
  form.post(`/documents/${documentId}/fulfill`, { forceFormData: true })
}
</script>

<template>
  <Head :title="caseRecord.case_number" />

  <div class="space-y-5">
    <section class="surface p-4 sm:p-5">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <div class="flex flex-wrap items-center gap-2">
            <h1 class="text-2xl font-semibold">{{ caseRecord.case_number }}</h1>
            <span :class="badgeClass(caseRecord.status)">{{ statusLabels[caseRecord.status] || caseRecord.status }}</span>
            <span class="badge-neutral capitalize">{{ String(caseRecord.case_type).replaceAll('_', ' ') }}</span>
          </div>
          <p class="mt-2 text-sm text-muted">{{ caseRecord.business?.registered_name }} · {{ caseRecord.business?.masked_tin || 'TIN masked' }}</p>
        </div>
        <Link href="/owner/dashboard" class="btn-secondary">Back to dashboard</Link>
      </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1fr_360px]">
      <div class="space-y-5">
        <div class="surface p-4 sm:p-5">
          <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-warning">
              <ShieldAlert class="h-5 w-5" />
            </span>
            <div>
              <h2 class="section-title">Preliminary result</h2>
              <p class="mt-1 text-sm leading-6 text-muted">{{ caseRecord.result?.generated_summary || caseRecord.summary }}</p>
            </div>
          </div>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Risk score</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.result?.risk_score ?? 0 }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Risk level</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.result?.risk_level || 'Pending' }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Result type</p>
              <p class="mt-1 text-sm font-semibold capitalize">{{ String(caseRecord.result?.result_type || '').replaceAll('_', ' ') }}</p>
            </div>
          </div>
          <div v-if="caseRecord.result?.recommendations?.length" class="mt-4">
            <h3 class="text-sm font-semibold">Recommendations</h3>
            <ul class="mt-2 space-y-2 text-sm text-muted">
              <li v-for="item in caseRecord.result.recommendations" :key="item" class="rounded-lg bg-slate-50 px-3 py-2">{{ item }}</li>
            </ul>
          </div>
        </div>

        <div class="surface p-4 sm:p-5">
          <h2 class="section-title">Checklist</h2>
          <div class="mt-3 space-y-3">
            <div v-for="item in caseRecord.checklist_items" :key="item.id" class="rounded-lg border border-line p-3">
              <div class="flex items-center justify-between gap-3">
                <p class="font-medium">{{ item.title }}</p>
                <span :class="item.status === 'done' ? 'badge-success' : item.status === 'blocked' ? 'badge-danger' : 'badge-neutral'">
                  {{ String(item.status).replaceAll('_', ' ') }}
                </span>
              </div>
              <p v-if="item.description" class="mt-1 text-sm text-muted">{{ item.description }}</p>
            </div>
          </div>
        </div>

        <div class="surface p-4 sm:p-5">
          <h2 class="section-title">Documents</h2>
          <form class="mt-4 grid gap-3 rounded-lg border border-line bg-slate-50 p-3 sm:grid-cols-[180px_1fr_auto]" @submit.prevent="uploadDocument">
            <select v-model="uploadForm.document_type" class="input">
              <option>Certificate of Registration</option>
              <option>Open case summary</option>
              <option>Filed tax returns</option>
              <option>Payment confirmations</option>
              <option>BIR notice</option>
              <option>Closure document</option>
              <option>Supporting document</option>
            </select>
            <input class="input" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" @input="uploadForm.document = $event.target.files[0]" />
            <button class="btn-primary" type="submit" :disabled="uploadForm.processing">
              <FileUp class="h-4 w-4" />
              Upload
            </button>
          </form>
          <p v-if="uploadForm.errors.document" class="error">{{ uploadForm.errors.document }}</p>

          <div v-if="requestedDocuments.length" class="mt-4">
            <h3 class="text-sm font-semibold">Requested documents</h3>
            <div class="mt-2 space-y-2">
              <div v-for="document in requestedDocuments" :key="document.id" class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                <p class="font-medium text-amber-950">{{ document.document_type }}</p>
                <p v-if="document.request_note" class="mt-1 text-sm text-amber-900">{{ document.request_note }}</p>
                <input class="mt-3 block w-full text-sm" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" @change="fulfill(document.id, $event)" />
              </div>
            </div>
          </div>

          <div class="mt-4 overflow-x-auto">
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
                <tr v-for="document in uploadedDocuments" :key="document.id">
                  <td>{{ document.document_type }}</td>
                  <td>{{ document.original_filename || 'Pending upload' }}</td>
                  <td><span class="badge-neutral">{{ document.status }}</span></td>
                  <td class="text-right">
                    <a v-if="document.file_path" :href="`/documents/${document.id}/download`" class="btn-secondary">
                      <Download class="h-4 w-4" />
                      Download
                    </a>
                  </td>
                </tr>
                <tr v-if="!uploadedDocuments.length">
                  <td colspan="4" class="text-muted">No uploaded documents yet.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <aside class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title flex items-center gap-2">
            <CreditCard class="h-5 w-5 text-primary" />
            Service fee
          </h2>
          <div v-if="payment" class="mt-3 rounded-lg border border-line p-3">
            <p class="font-semibold">{{ payment.service_package?.name }}</p>
            <p class="mt-1 text-2xl font-semibold">{{ money(payment.amount) }}</p>
            <p class="mt-1 text-sm capitalize text-muted">{{ String(payment.status).replaceAll('_', ' ') }}</p>
          </div>
          <div v-else class="mt-3 space-y-3">
            <button
              v-for="pkg in packages"
              :key="pkg.id"
              type="button"
              class="w-full rounded-lg border border-line bg-white p-3 text-left transition hover:border-primary"
              :class="{ 'border-primary bg-teal-50': selectedPackageId === pkg.id }"
              @click="selectPackage(pkg.id)"
            >
              <span class="block font-semibold">{{ pkg.name }}</span>
              <span class="mt-1 block text-sm text-muted">{{ pkg.description }}</span>
              <span class="mt-2 block font-semibold">{{ money(pkg.price) }}</span>
            </button>
          </div>

          <form v-if="payment?.status === 'pending'" class="mt-4 space-y-3" @submit.prevent="submitReference">
            <div>
              <label class="label" for="provider_reference">Payment reference</label>
              <input id="provider_reference" v-model="referenceForm.provider_reference" class="input mt-1" placeholder="Bank, GCash, or manual reference" />
              <p v-if="referenceForm.errors.provider_reference" class="error">{{ referenceForm.errors.provider_reference }}</p>
            </div>
            <button class="btn-primary w-full" type="submit" :disabled="referenceForm.processing">
              <Send class="h-4 w-4" />
              Submit reference
            </button>
          </form>

          <p class="mt-3 text-xs leading-5 text-muted">
            BIR taxes, penalties, government fees, and filing costs are separate from Open Case service fees.
          </p>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Accountant</h2>
          <p class="mt-2 text-sm text-muted">
            {{ caseRecord.assigned_accountant ? caseRecord.assigned_accountant.name : 'Pending admin assignment after payment verification.' }}
          </p>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Client notes</h2>
          <div v-if="clientNotes.length" class="mt-3 space-y-3">
            <div v-for="note in clientNotes" :key="note.id" class="rounded-lg bg-slate-50 p-3">
              <p class="text-sm">{{ note.note }}</p>
              <p class="mt-2 text-xs text-muted">{{ note.user?.name }} · {{ new Date(note.created_at).toLocaleString() }}</p>
            </div>
          </div>
          <p v-else class="mt-2 text-sm text-muted">No client-visible notes yet.</p>
        </div>
      </aside>
    </section>
  </div>
</template>
