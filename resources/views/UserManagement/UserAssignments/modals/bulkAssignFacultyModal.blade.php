@props([
    'departmentId',
    'departmentName',
    'potentialFaculty' => [],
    'assignedFacultyIds' => [],
])

<x-modal.dialog id="bulkAssignFacultyModal-{{ $departmentId }}" maxWidth="max-w-2xl" width="w-11/12" maxHeight="max-h-[90vh]" variant="assign">
    <form method="POST" action="{{ route('user-assignments.bulk-assign-faculty') }}" class="flex flex-col"
        x-data="{
            submitting: false,
            searchQuery: '',
            selectedUsers: [],
            get filteredUsers() {
                if (!this.searchQuery.trim()) return {{ json_encode($potentialFaculty) }};
                const query = this.searchQuery.toLowerCase();
                return {{ json_encode($potentialFaculty) }}.filter(user => 
                    user.name.toLowerCase().includes(query) || 
                    user.email.toLowerCase().includes(query)
                );
            },
            get assignedFaculty() {
                return {{ json_encode($assignedFacultyIds) }};
            },
            get availableUsers() {
                return this.filteredUsers.filter(user => !this.assignedFaculty.includes(user.id));
            },
            toggleUser(userId) {
                if (this.selectedUsers.includes(userId)) {
                    this.selectedUsers = this.selectedUsers.filter(id => id !== userId);
                } else {
                    this.selectedUsers.push(userId);
                }
            },
            toggleAll() {
                if (this.selectedUsers.length === this.availableUsers.length) {
                    this.selectedUsers = [];
                } else {
                    this.selectedUsers = this.availableUsers.map(user => user.id);
                }
            },
            get allSelected() {
                return this.availableUsers.length > 0 && this.selectedUsers.length === this.availableUsers.length;
            }
        }"
        x-on:submit="submitting = true">
        @csrf
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        {{-- Hidden inputs for selected users --}}
        <div x-show="false">
            <template x-for="userId in selectedUsers" :key="userId">
                <input type="hidden" name="user_ids[]" :value="userId">
            </template>
        </div>

        <x-modal.header :modalId="'bulkAssignFacultyModal-' . $departmentId" variant="assign">
            <div class="min-w-0">
                <p class="text-[15px] font-bold text-[#0f172a]">Assign Faculty Members</p>
                <p class="text-[13px] text-[#94a3b8] truncate">{{ $departmentName }}</p>
            </div>
        </x-modal.header>

        <x-modal.body>
            <div class="space-y-4">
                {{-- Search input --}}
                <div>
                    <x-modal.modal-label for="facultySearch{{ $departmentId }}">Search Faculty</x-modal.modal-label>
                    <div class="relative mt-2">
                        <input
                            type="text"
                            id="facultySearch{{ $departmentId }}"
                            x-model="searchQuery"
                            ::disabled="submitting"
                            ::class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-2 text-[13px] border border-[#E3E8EB] rounded-lg bg-white
                                   focus:outline-none focus:ring-2 focus:ring-[#00C075] focus:border-transparent">
                        <i class="bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8]"></i>
                    </div>
                </div>

                {{-- Select all checkbox --}}
                <div x-show="availableUsers.length > 0" class="flex items-center gap-2 px-3 py-2 bg-[#F9FAFA] rounded-lg border border-[#E3E8EB]">
                    <input
                        type="checkbox"
                        x-model="allSelected"
                        @change="toggleAll()"
                        :disabled="submitting || availableUsers.length === 0"
                        class="w-4 h-4 rounded text-[#00C075] border-[#C8D0DA] focus:ring-[#00C075] focus:ring-offset-0">
                    <span class="text-[12px] font-medium text-[#394056]">
                        Select All <span class="text-[#93A1AF] font-normal">(<span x-text="availableUsers.length"></span> available)</span>
                    </span>
                </div>

                {{-- User list --}}
                <div class="border border-[#E3E8EB] rounded-lg divide-y divide-[#F1F3F5] overflow-y-auto max-h-64"
                    ::class="submitting ? 'opacity-60 cursor-not-allowed pointer-events-none' : ''">
                    
                    <template x-if="availableUsers.length === 0">
                        <div class="p-4 text-center text-[13px] text-[#94a3b8]">
                            <template x-if="searchQuery.trim()">
                                <span>No faculty members match your search.</span>
                            </template>
                            <template x-if="!searchQuery.trim()">
                                <span>No available faculty members to assign.</span>
                            </template>
                        </div>
                    </template>

                    <template x-for="user in availableUsers" :key="user.id">
                        <label class="flex items-center gap-3 px-3 py-2.5 hover:bg-[#F9FAFA] cursor-pointer transition-colors duration-100">
                            <input
                                type="checkbox"
                                :value="user.id"
                                x-model="selectedUsers"
                                :disabled="submitting"
                                class="w-4 h-4 rounded text-[#00C075] border-[#C8D0DA] focus:ring-[#00C075] focus:ring-offset-0 shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-[13px] font-medium text-[#394056] truncate" x-text="user.name"></p>
                                <p class="text-[11px] text-[#93A1AF] truncate" x-text="user.email"></p>
                            </div>
                        </label>
                    </template>
                </div>

                {{-- Selection summary --}}
                <div x-show="selectedUsers.length > 0" class="flex items-center justify-between px-3 py-2 bg-[#EDFFF8] rounded-lg border border-[#00C075]">
                    <span class="text-[12px] font-medium text-[#06754E]">
                        <span x-text="selectedUsers.length"></span> faculty member<span x-text="selectedUsers.length !== 1 ? 's' : ''"></span> selected
                    </span>
                </div>

                @error('user_ids')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </x-modal.body>

        <x-modal.footer>
            <x-modal.close-button :modalId="'bulkAssignFacultyModal-' . $departmentId" text="Cancel" ::disabled="submitting" />
            <x-ui.button type="submit" variant="save"
                submitting="submitting" loadingText="Assigning…"
                ::disabled="submitting || selectedUsers.length === 0">
                <i class="bx bx-user-check"></i> Assign Selected (<span x-text="selectedUsers.length"></span>)
            </x-ui.button>
        </x-modal.footer>
    </form>
</x-modal.dialog>
