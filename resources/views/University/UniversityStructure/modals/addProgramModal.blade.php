@php $hasErrors = $errors->hasAny(['name', 'primary_department_id', 'bor_approval_no', 'bor_approval_date']) && session('_modal') === 'addProgram'; @endphp

<x-modal.dialog id="addProgramModal" maxWidth="max-w-6xl" width="w-2xl sm:w-full" variant="add">
    <x-modal.header modalId="addProgramModal" variant="add">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Add New Program</p>
            <p class="text-[13px] text-[#94a3b8]">Assign a primary department and optional supporting departments.</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.program.store') }}" class="flex flex-col min-h-0"
        x-data="{
            submitting: false,
            name: @js(old('name', '')),
            primaryDept: @js(old('primary_department_id', '')),
            supportingDepts: @js(old('supporting_department_ids', []))
        }"
        x-on:submit="submitting = true"
        x-init="@js($hasErrors) && $nextTick(() => document.getElementById('addProgramModal')?.showModal())">
        @csrf
        <input type="hidden" name="_modal" value="addProgram">

        <x-modal.body>
            {{-- Generic / catch-block errors --}}
            @if ($errors->has('error'))
                <x-feedback-status.alert type="error" :showTitle="false" class="mb-4">
                    <strong>Something went wrong:</strong> {{ $errors->first('error') }}
                </x-feedback-status.alert>
            @endif

            {{-- Validation summary --}}
            @if ($hasErrors)
                <x-feedback-status.alert type="error" :showTitle="false" class="mb-4">
                    Please fix the highlighted fields below before submitting.
                </x-feedback-status.alert>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4">

                {{-- LEFT COLUMN --}}
                <div class="flex flex-col gap-4">

                    <div>
                        <x-modal.modal-label isRequired>Program Name</x-modal.modal-label>
                        <x-form.input
                            type="text"
                            name="name"
                            value="{{ old('name', '') }}"
                            placeholder="e.g. BS Computer Science"
                            x-model="name"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            required />
                        @if ($hasErrors)
                            @error('name')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label for="add_primary_department_id" isRequired>Primary Department</x-modal.modal-label>
                        <input type="hidden" name="primary_department_id" x-model="primaryDept">
                        <select
                            id="add_primary_department_id"
                            x-model="primaryDept"
                            :disabled="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="w-full px-3 py-2 text-[13px] border border-[#E3E8EB] rounded-lg bg-white
                                   focus:outline-none focus:ring-2 focus:ring-[#00C075] focus:border-transparent">
                            <option value="">Select primary department</option>
                            @foreach($allDepartments->groupBy('college.name') as $collegeName => $depts)
                                <optgroup label="{{ $collegeName }}">
                                    @foreach($depts as $dept)
                                        <option value="{{ $dept->id }}" {{ old('primary_department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-[#93A1AF] mt-1">Main department responsible for this program</p>
                        @if ($hasErrors)
                            @error('primary_department_id')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label>BOR Approval Resolution No.</x-modal.modal-label>
                        <x-form.input
                            type="text"
                            name="bor_approval_no"
                            value="{{ old('bor_approval_no', '') }}"
                            placeholder="e.g. BOR Res. No. 123"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if ($hasErrors)
                            @error('bor_approval_no')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <x-modal.modal-label>BOR Approval Date</x-modal.modal-label>
                        <x-form.input
                            type="date"
                            name="bor_approval_date"
                            value="{{ old('bor_approval_date', '') }}"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                        @if ($hasErrors)
                            @error('bor_approval_date')
                                <p class="text-xs text-[#E52F28] flex items-center gap-1 mt-1">
                                    <i class="bx bx-error-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                </div>

                {{-- RIGHT COLUMN — Supporting Departments --}}
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
                                            {{ in_array($dept->id, old('supporting_department_ids', [])) ? 'checked' : '' }}
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
            <x-modal.close-button modalId="addProgramModal" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="add-button"
                submitting="submitting" loadingText="Creating…"
                ::disabled="submitting || !name.trim() || !primaryDept">
                <i class="bx bx-save"></i> Create Program
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
