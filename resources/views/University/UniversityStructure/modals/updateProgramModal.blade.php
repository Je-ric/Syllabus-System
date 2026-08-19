@php
    $editErrors  = $errors->hasAny(['name', 'primary_department_id', 'bor_approval_no', 'bor_approval_date'])
                   && str_starts_with(session('_modal', ''), 'updateProgram_');
    $editModalId = session('_modal', '');
@endphp

<x-modal.dialog id="updateProgramModal" maxWidth="max-w-6xl" width="w-2xl sm:w-full" variant="edit">
    <x-modal.header modalId="updateProgramModal" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit Program</p>
            <p id="updateProgramModal_subtitle" class="text-[13px] text-[#94a3b8] truncate max-w-xs"></p>
        </div>
    </x-modal.header>

    <form id="updateProgramModal_form" method="POST" class="flex flex-col min-h-0"
        x-data="{
            submitting: false,
            name: '',
            primaryDept: '',
            supportingDepts: [],
            borNo: '',
            borDate: '',
            origName: '',
            origPrimaryDept: '',
            origSupportingDepts: [],
            origBorNo: '',
            origBorDate: '',
            get hasChanged() {
                if (this.name.trim() !== this.origName.trim()) return true;
                if (this.primaryDept !== this.origPrimaryDept) return true;
                if (this.borNo.trim() !== this.origBorNo.trim()) return true;
                if (this.borDate !== this.origBorDate) return true;
                const a = [...this.supportingDepts].sort();
                const b = [...this.origSupportingDepts].sort();
                return JSON.stringify(a) !== JSON.stringify(b);
            }
        }"
        x-on:submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_modal" id="updateProgramModal_modalKey" value="">
        <input type="hidden" name="primary_department_id" x-model="primaryDept">

        <x-modal.body>
            @if ($editErrors)
                <x-feedback-status.alert type="error" :showTitle="false" class="mb-4">
                    Please fix the highlighted fields below before submitting.
                </x-feedback-status.alert>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4">

                {{-- LEFT COLUMN --}}
                <div class="flex flex-col gap-4">

                    <div>
                        <x-modal.modal-label for="updateProgramModal_name" isRequired>Program Name</x-modal.modal-label>
                        <x-form.input
                            id="updateProgramModal_name"
                            type="text"
                            name="name"
                            x-model="name"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            required />
                        @if ($editErrors)
                            @error('name')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label for="updateProgramModal_primaryDept" isRequired>Primary Department</x-modal.modal-label>
                        <select
                            id="updateProgramModal_primaryDept"
                            x-model="primaryDept"
                            :disabled="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="w-full px-3 py-2 text-[13px] border border-[#E3E8EB] rounded-lg bg-white
                                   focus:outline-none focus:ring-2 focus:ring-[#00C075] focus:border-transparent">
                            <option value="">Select primary department</option>
                            @foreach($allDepartments->groupBy('college.name') as $collegeName => $depts)
                                <optgroup label="{{ $collegeName }}">
                                    @foreach($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-[#93A1AF] mt-1">Main department responsible for this program</p>
                        @if ($editErrors)
                            @error('primary_department_id')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label for="updateProgramModal_borNo">BOR Approval Resolution No.</x-modal.modal-label>
                        <x-form.input
                            id="updateProgramModal_borNo"
                            type="text"
                            name="bor_approval_no"
                            x-model="borNo"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if ($editErrors)
                            @error('bor_approval_no')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label for="updateProgramModal_borDate">BOR Approval Date</x-modal.modal-label>
                        <x-form.input
                            id="updateProgramModal_borDate"
                            type="date"
                            name="bor_approval_date"
                            x-model="borDate"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if ($editErrors)
                            @error('bor_approval_date')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                </div>

                {{-- RIGHT COLUMN — Supporting Departments (rendered once) --}}
                <div class="flex flex-col gap-1">
                    <x-modal.modal-label>Supporting Departments <span class="text-[#93A1AF] font-normal">(Optional)</span></x-modal.modal-label>
                    <p class="text-[11px] text-[#93A1AF] mb-2">Departments that also contribute to this program</p>

                    <div class="border border-[#E3E8EB] rounded-lg divide-y divide-[#F1F3F5] overflow-y-auto max-h-64"
                        ::class="submitting ? 'opacity-60 cursor-not-allowed pointer-events-none' : ''">
                        @foreach($allDepartments->groupBy('college.name') as $collegeName => $depts)
                            <div>
                                <p class="text-[10.5px] font-bold uppercase tracking-wide text-[#72809E]
                                          bg-[#F9FAFA] px-3 py-1.5 border-b border-[#F1F3F5]">
                                    {{ $collegeName }}
                                </p>
                                @foreach($depts as $dept)
                                    <label class="flex items-center gap-2.5 px-3 py-2 hover:bg-[#F9FAFA]
                                                  cursor-pointer transition-colors duration-100"
                                           :class="{ 'opacity-40 cursor-not-allowed pointer-events-none': primaryDept == '{{ $dept->id }}' }">
                                        <input
                                            type="checkbox"
                                            name="supporting_department_ids[]"
                                            value="{{ $dept->id }}"
                                            x-model="supportingDepts"
                                            :disabled="primaryDept == '{{ $dept->id }}'"
                                            class="w-4 h-4 rounded text-[#00C075] border-[#C8D0DA]
                                                   focus:ring-[#00C075] focus:ring-offset-0 shrink-0">
                                        <span class="text-[12px] text-[#394056] leading-snug">{{ $dept->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button modalId="updateProgramModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || !primaryDept || !hasChanged">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>

<script>
function openEditProgramModal(id, name, primaryDept, supportingDepts, borNo, borDate, routeBase) {
    const modal = document.getElementById('updateProgramModal');
    const form  = document.getElementById('updateProgramModal_form');
    form.action = routeBase + '/' + id;
    document.getElementById('updateProgramModal_subtitle').textContent = name;
    document.getElementById('updateProgramModal_modalKey').value = 'updateProgram_' + id;
    const data = Alpine.$data(form);
    data.name               = name;
    data.primaryDept        = String(primaryDept);
    data.supportingDepts    = supportingDepts.map(String);
    data.borNo              = borNo;
    data.borDate            = borDate;
    data.origName           = name;
    data.origPrimaryDept    = String(primaryDept);
    data.origSupportingDepts = supportingDepts.map(String);
    data.origBorNo          = borNo;
    data.origBorDate        = borDate;
    data.submitting         = false;
    modal.showModal();
}

@if ($editErrors)
    document.addEventListener('alpine:init', () => {
        openEditProgramModal(
            @js(explode('_', $editModalId)[1] ?? ''),
            @js(old('name', '')),
            @js(old('primary_department_id', '')),
            @js(old('supporting_department_ids', [])),
            @js(old('bor_approval_no', '')),
            @js(old('bor_approval_date', '')),
            '{{ rtrim(url('/university-structure/program'), '/') }}'
        );
    });
@endif
</script>
