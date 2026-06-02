<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { FileCheck2, Save } from '@lucide/vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  businesses: { type: Array, default: () => [] },
})

const form = useForm({
  business_id: '',
  registered_name: '',
  trade_name: '',
  tin: '',
  rdo_code: '',
  business_type: 'single_proprietor',
  taxpayer_type: 'individual',
  registered_address: '',
  registration_date: '',
  business_activity: '',
  line_of_business: '',
  business_status: 'active',
  last_year_of_operation: '',
  still_operating: 'yes',
  primary_goal: 'open_case_review',
  case_type: 'open_case_review',
  filed_closure: 'no',
  received_bir_notice: 'no',
  has_cor: 'yes',
  knows_rdo: 'yes',
  has_unused_receipts: 'not_sure',
  has_books: 'not_sure',
  last_filed_year: '',
  tax_types: [],
  documents: [],
  document_types: [],
  disclaimer_accepted: false,
})

const selectedFiles = computed(() => Array.from(form.documents || []))

function loadBusiness() {
  const business = props.businesses.find((item) => String(item.id) === String(form.business_id))
  if (!business) return

  form.registered_name = business.registered_name || ''
  form.trade_name = business.trade_name || ''
  form.rdo_code = business.rdo_code || ''
  form.business_type = business.business_type || 'single_proprietor'
  form.taxpayer_type = business.taxpayer_type || 'individual'
  form.registered_address = business.registered_address || ''
  form.registration_date = business.registration_date || ''
  form.business_activity = business.business_activity || ''
  form.line_of_business = business.line_of_business || ''
  form.business_status = business.business_status || 'active'
  form.last_year_of_operation = business.last_year_of_operation || ''
  form.still_operating = business.still_operating ? 'yes' : 'no'
  form.primary_goal = business.primary_goal || 'open_case_review'
}

function updateFiles(event) {
  form.documents = Array.from(event.target.files || [])
  form.document_types = form.documents.map(() => 'Supporting document')
}

function submit() {
  form.post('/cases', { forceFormData: true })
}
</script>

<template>
  <Head title="Start Case" />

  <form class="space-y-5" @submit.prevent="submit">
    <section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Start case</h1>
        <p class="mt-1 text-sm text-muted">Create a business profile, answer the intake questions, and receive a preliminary result.</p>
      </div>
      <button class="btn-primary" type="submit" :disabled="form.processing">
        <Save class="h-4 w-4" />
        Generate result
      </button>
    </section>

    <section class="surface p-4 sm:p-5">
      <h2 class="section-title">Business profile</h2>
      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div v-if="businesses.length" class="sm:col-span-2">
          <label class="label" for="business_id">Use existing profile</label>
          <select id="business_id" v-model="form.business_id" class="input mt-1" @change="loadBusiness">
            <option value="">New business profile</option>
            <option v-for="business in businesses" :key="business.id" :value="business.id">{{ business.registered_name }}</option>
          </select>
        </div>
        <div>
          <label class="label" for="registered_name">Registered business name</label>
          <input id="registered_name" v-model="form.registered_name" class="input mt-1" required />
          <p v-if="form.errors.registered_name" class="error">{{ form.errors.registered_name }}</p>
        </div>
        <div>
          <label class="label" for="trade_name">Trade name</label>
          <input id="trade_name" v-model="form.trade_name" class="input mt-1" />
        </div>
        <div>
          <label class="label" for="tin">TIN</label>
          <input id="tin" v-model="form.tin" class="input mt-1" placeholder="Encrypted after submission" />
          <p v-if="form.errors.tin" class="error">{{ form.errors.tin }}</p>
        </div>
        <div>
          <label class="label" for="rdo_code">RDO code</label>
          <input id="rdo_code" v-model="form.rdo_code" class="input mt-1" />
        </div>
        <div>
          <label class="label" for="business_type">Business type</label>
          <select id="business_type" v-model="form.business_type" class="input mt-1">
            <option value="single_proprietor">Single proprietor</option>
            <option value="freelancer">Freelancer</option>
            <option value="corporation">Corporation</option>
            <option value="partnership">Partnership</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div>
          <label class="label" for="business_status">Business status</label>
          <select id="business_status" v-model="form.business_status" class="input mt-1">
            <option value="active">Active</option>
            <option value="stopped_operating">Stopped operating</option>
            <option value="for_closure">For closure</option>
            <option value="closed">Closed</option>
          </select>
        </div>
        <div>
          <label class="label" for="registration_date">Registration date</label>
          <input id="registration_date" v-model="form.registration_date" class="input mt-1" type="date" />
        </div>
        <div>
          <label class="label" for="last_year_of_operation">Last year of operation</label>
          <input id="last_year_of_operation" v-model="form.last_year_of_operation" class="input mt-1" type="number" min="1900" />
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="registered_address">Registered address</label>
          <textarea id="registered_address" v-model="form.registered_address" class="input mt-1 min-h-20"></textarea>
        </div>
        <div>
          <label class="label" for="line_of_business">Line of business</label>
          <input id="line_of_business" v-model="form.line_of_business" class="input mt-1" />
        </div>
        <div>
          <label class="label" for="primary_goal">Primary goal</label>
          <select id="primary_goal" v-model="form.primary_goal" class="input mt-1">
            <option value="open_case_review">Open case review</option>
            <option value="business_closure">Business closure</option>
            <option value="missing_filing_review">Missing filing review</option>
            <option value="compliance_cleanup">Compliance cleanup</option>
            <option value="general_consultation">General consultation</option>
          </select>
        </div>
      </div>
    </section>

    <section class="surface p-4 sm:p-5">
      <h2 class="section-title">Case questions</h2>
      <div class="mt-4 grid gap-4 sm:grid-cols-2">
        <div>
          <label class="label" for="case_type">Case type</label>
          <select id="case_type" v-model="form.case_type" class="input mt-1">
            <option value="business_closure">Business closure</option>
            <option value="open_case_review">Open case review</option>
            <option value="missing_filing_review">Missing filing review</option>
            <option value="tax_type_clarification">Tax type clarification</option>
            <option value="registration_update">Registration update</option>
            <option value="compliance_cleanup">Compliance cleanup</option>
            <option value="general_consultation">General BIR consultation</option>
          </select>
        </div>
        <div>
          <label class="label" for="still_operating">Still operating?</label>
          <select id="still_operating" v-model="form.still_operating" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
        <div>
          <label class="label" for="filed_closure">Filed for closure?</label>
          <select id="filed_closure" v-model="form.filed_closure" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
            <option value="not_sure">Not sure</option>
          </select>
        </div>
        <div>
          <label class="label" for="received_bir_notice">Received BIR notice?</label>
          <select id="received_bir_notice" v-model="form.received_bir_notice" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
        <div>
          <label class="label" for="has_cor">Has Certificate of Registration?</label>
          <select id="has_cor" v-model="form.has_cor" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
        <div>
          <label class="label" for="knows_rdo">Knows RDO?</label>
          <select id="knows_rdo" v-model="form.knows_rdo" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>
        <div>
          <label class="label" for="has_unused_receipts">Unused receipts or invoices?</label>
          <select id="has_unused_receipts" v-model="form.has_unused_receipts" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
            <option value="not_sure">Not sure</option>
          </select>
        </div>
        <div>
          <label class="label" for="has_books">Books of accounts available?</label>
          <select id="has_books" v-model="form.has_books" class="input mt-1">
            <option value="yes">Yes</option>
            <option value="no">No</option>
            <option value="not_sure">Not sure</option>
          </select>
        </div>
        <div>
          <label class="label" for="last_filed_year">Last filed return year</label>
          <input id="last_filed_year" v-model="form.last_filed_year" class="input mt-1" type="number" min="1900" />
        </div>
        <div>
          <p class="label">Tax types on COR</p>
          <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
            <label v-for="taxType in ['2551Q', '1701Q', '1701', '0605', 'Withholding', 'VAT']" :key="taxType" class="flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2">
              <input v-model="form.tax_types" type="checkbox" :value="taxType" class="h-4 w-4 rounded border-line text-primary" />
              {{ taxType }}
            </label>
          </div>
        </div>
      </div>
    </section>

    <section class="surface p-4 sm:p-5">
      <h2 class="section-title">Documents</h2>
      <div class="mt-4 rounded-lg border border-dashed border-line bg-slate-50 p-4 cursor-pointer">
        <label class="label" for="documents">Upload available documents</label>
        <input id="documents" class="mt-2 block w-full text-sm" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" @change="updateFiles" />
        <div v-if="selectedFiles.length" class="mt-3 space-y-2">
          <div v-for="(file, index) in selectedFiles" :key="file.name + index" class="grid gap-2 rounded-lg bg-white p-3 sm:grid-cols-[1fr_220px]">
            <div class="flex items-center gap-2 text-sm">
              <FileCheck2 class="h-4 w-4 text-primary" />
              {{ file.name }}
            </div>
            <select v-model="form.document_types[index]" class="input">
              <option>Certificate of Registration</option>
              <option>Open case summary</option>
              <option>Filed tax returns</option>
              <option>Payment confirmations</option>
              <option>BIR notice</option>
              <option>Closure document</option>
              <option>Supporting document</option>
            </select>
          </div>
        </div>
      </div>
      <p v-if="form.errors.documents" class="error">{{ form.errors.documents }}</p>
    </section>

    <section class="surface p-4 sm:p-5">
      <label class="flex gap-3 text-sm leading-6 text-muted">
        <input v-model="form.disclaimer_accepted" type="checkbox" class="mt-1 h-4 w-4 rounded border-line text-primary" />
        <span>I understand Open Case gives a preliminary review only and is not connected to BIR. Final tax advice must come from a qualified professional or BIR officer.</span>
      </label>
      <p v-if="form.errors.disclaimer_accepted" class="error">{{ form.errors.disclaimer_accepted }}</p>
      <div class="mt-4 flex justify-end">
        <button class="btn-primary" type="submit" :disabled="form.processing">
          <Save class="h-4 w-4" />
          Generate result
        </button>
      </div>
    </section>
  </form>
</template>
