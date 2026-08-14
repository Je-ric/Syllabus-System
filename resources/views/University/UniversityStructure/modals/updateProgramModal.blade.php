@php
    $primaryDeptId     = $program->departments->where('pivot.role', 'primary')->first()?->id;
    $supportingDeptIds = $program->departments->where('pivot.role', 'supporting')->pluck('id')->toArray();
@endphp

<x-modal.dialog id="updateProgramModal_{{ $program->id }}" maxWidth="max-w-6xl" width="w-2xl sm:w-full" variant="edit">
    <x-modal.header modalId="updateProgramModal_{{ $program->id }}" variant="edit">
        <div>
            <p class="text-[15px] font-bold text-[#0f172a]">Edit Program</p>
            <p class="text-[13px] text-[#94a3b8] truncate max-w-xs">{{ $program->name }}</p>
        </div>
    </x-modal.header>

    <form method="POST" action="{{ route('university.structure.program.update', $program) }}" class="flex flex-col min-h-0"
        x-data="{
            submitting: false,
            name: @js($program->name),
            primaryDept: @js((string) ($primaryDeptId ?? '')),
            supportingDepts: @js(array_map('strval', $supportingDeptIds)),
            borNo: @js($program->bor_approval_no ?? ''),
            borDate: @js($program->bor_approval_date ?? ''),
            origName: @js($program->name),
            origPrimaryDept: @js((string) ($primaryDeptId ?? '')),
            origSupportingDepts: @js(array_map('strval', $supportingDeptIds)),
            origBorNo: @js($program->bor_approval_no ?? ''),
            origBorDate: @js($program->bor_approval_date ?? ''),
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

        <x-modal.body>
            {{-- Two-column grid on md+, single column on mobile --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4">

                {{-- LEFT COLUMN --}}
                <div class="flex flex-col gap-4">

                    <div>
                        <x-modal.modal-label for="editProgramName_{{ $program->id }}" isRequired>Program Name</x-modal.modal-label>
                        <x-form.input
                            id="editProgramName_{{ $program->id }}"
                            type="text"
                            name="name"
                            value="{{ $program->name }}"
                            x-model="name"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            required />
                    </div>

                    <div>
                        <x-modal.modal-label for="editPrimaryDept_{{ $program->id }}" isRequired>Primary Department</x-modal.modal-label>
                        <input type="hidden" name="primary_department_id" x-model="primaryDept">
                        <select
                            id="editPrimaryDept_{{ $program->id }}"
                            x-model="primaryDept"
                            :disabled="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="w-full px-3 py-2 text-[13px] border border-[#E3E8EB] rounded-lg bg-white
                                   focus:outline-none focus:ring-2 focus:ring-[#00C075] focus:border-transparent">
                            <option value="">Select primary department</option>
                            @foreach($allDepartments->groupBy('college.name') as $collegeName => $depts)
                                <optgroup label="{{ $collegeName }}">
                                    @foreach($depts as $dept)
                                        <option value="{{ $dept->id }}" {{ $dept->id == $primaryDeptId ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-[#93A1AF] mt-1">Main department responsible for this program</p>
                    </div>

                    <div>
                        <x-modal.modal-label for="editBorNo_{{ $program->id }}">BOR Approval Resolution No.</x-modal.modal-label>
                        <x-form.input
                            id="editBorNo_{{ $program->id }}"
                            type="text"
                            name="bor_approval_no"
                            value="{{ $program->bor_approval_no }}"
                            x-model="borNo"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
                    </div>

                    <div>
                        <x-modal.modal-label for="editBorDate_{{ $program->id }}">BOR Approval Date</x-modal.modal-label>
                        <x-form.input
                            id="editBorDate_{{ $program->id }}"
                            type="date"
                            name="bor_approval_date"
                            value="{{ $program->bor_approval_date }}"
                            x-model="borDate"
                            ::readonly="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''" />
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
            <x-modal.close-button :modalId="'updateProgramModal_' . $program->id" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Saving…"
                ::disabled="submitting || !name.trim() || !primaryDept || !hasChanged">
                <i class="bx bx-save"></i> Save Changes
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
