@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Academic Structure Management</h1>

    <div class="grid grid-cols-12 gap-6">
        {{-- LEFT COLUMN: Hierarchical Tree --}}
        <div class="col-span-5 space-y-4">
            <x-button variant="add-button" onclick="document.getElementById('addCollegeModal').showModal()">
                <i class="bx bx-plus"></i> Add College
            </x-button>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div id="collegeAccordions" class="space-y-2">
                    @foreach ($colleges as $college)
                        {{-- College Item --}}
                        <details class="group border-l-4 border-green-500 bg-gray-50 rounded-r-lg" data-college-id="{{ $college->id }}">
                            <summary class="flex items-center gap-2 p-3 cursor-pointer select-none hover:bg-gray-100 rounded-r-lg transition-colors">
                                <i class="bx bx-chevron-right text-xl text-gray-500 group-open:rotate-90 transition-transform"></i>
                                <i class="bx bxs-school text-xl text-green-600"></i>
                                <span class="font-semibold text-gray-800 flex-1">{{ $college->name }}</span>
                                <button type="button"
                                        onclick="event.stopPropagation(); selectItem('college', {{ $college->id }}, '{{ addslashes($college->name) }}')"
                                        class="p-1.5 hover:bg-green-100 rounded transition-colors"
                                        title="View Details">
                                    <i class="bx bx-info-circle text-lg text-green-600"></i>
                                </button>
                            </summary>

                            <div class="pl-8 pr-3 pb-2 space-y-2">
                                <button type="button"
                                        onclick="openAddDepartmentModal({{ $college->id }})"
                                        class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                                    <i class="bx bx-plus-circle"></i> Add Department
                                </button>

                                @foreach ($departments->where('college_id', $college->id) as $dept)
                                    {{-- Department Item --}}
                                    <details class="group/dept border-l-4 border-red-400 bg-white rounded-r-lg" data-college-id="{{ $college->id }}" data-department-id="{{ $dept->id }}">
                                        <summary class="flex items-center gap-2 p-2 cursor-pointer select-none hover:bg-red-50 rounded-r-lg transition-colors">
                                            <i class="bx bx-chevron-right text-lg text-gray-500 group-open/dept:rotate-90 transition-transform"></i>
                                            <i class="bx bx-building text-lg text-red-500"></i>
                                            <span class="font-medium text-gray-700 flex-1 text-sm">{{ $dept->name }}</span>
                                            <button type="button"
                                                    onclick="event.stopPropagation(); selectItem('department', {{ $dept->id }}, '{{ addslashes($dept->name) }}', {{ $college->id }}, '{{ addslashes($college->name) }}')"
                                                    class="p-1 hover:bg-red-100 rounded transition-colors"
                                                    title="View Details">
                                                <i class="bx bx-info-circle text-red-500"></i>
                                            </button>
                                        </summary>

                                        <div class="pl-6 pr-2 pb-2 space-y-1">
                                            <button type="button"
                                                    onclick="openAddProgramModal({{ $dept->id }})"
                                                    class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                                <i class="bx bx-plus-circle"></i> Add Program
                                            </button>

                                            @foreach ($dept->programs as $program)
                                                {{-- Program Item --}}
                                                <div class="program-item flex items-center gap-2 p-2 hover:bg-blue-50 rounded-lg cursor-pointer transition-colors border-l-2 border-blue-400 bg-gray-50"
                                                     data-department-id="{{ $dept->id }}"
                                                     onclick="selectItem('program', {{ $program->id }}, '{{ addslashes($program->name) }}', {{ $dept->id }}, '{{ addslashes($dept->name) }}', {{ $college->id }}, '{{ addslashes($college->name) }}', '{{ $program->bor_approval_no }}', '{{ $program->bor_approval_date }}')">
                                                    <i class="bx bx-book-open text-blue-500"></i>
                                                    <span class="text-sm text-gray-700 flex-1">{{ $program->name }}</span>
                                                    <i class="bx bx-chevron-right text-gray-400"></i>
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Details & Actions Panel --}}
        <div class="col-span-7">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm sticky top-6">
                {{-- Default Empty State --}}
                <div id="emptyState" class="p-12 text-center">
                    <i class="bx bx-select-multiple text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Item Selected</h3>
                    <p class="text-gray-500">Select a college, department, or program from the left to view and edit details</p>
                </div>

                {{-- College Details Panel --}}
                <div id="collegeDetails" class="hidden">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-green-50 to-white p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <i class="bx bxs-school text-3xl text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide">College</div>
                                    <h2 id="collegeName" class="text-2xl font-bold text-gray-800 mt-1"></h2>
                                </div>
                            </div>
                            <button type="button"
                                    onclick="toggleEdit('college')"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                                <i class="bx bx-edit"></i> Edit College
                            </button>
                        </div>
                    </div>

                    {{-- View Mode --}}
                    <div id="collegeView" class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="text-sm text-gray-500 mb-1">College Name</div>
                                    <div id="collegeNameView" class="text-lg font-semibold text-gray-800"></div>
                                </div>
                            </div>
                            <div>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="text-sm text-blue-600 mb-1">Total Departments</div>
                                    <div id="collegeDeptCount" class="text-2xl font-bold text-blue-700"></div>
                                </div>
                            </div>
                            <div>
                                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                    <div class="text-sm text-purple-600 mb-1">Total Programs</div>
                                    <div id="collegeProgramCount" class="text-2xl font-bold text-purple-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Mode --}}
                    <div id="collegeEdit" class="hidden p-6">
                        <form id="collegeEditForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">College Name</label>
                                    <input type="text"
                                           id="collegeEditName"
                                           name="name"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           required>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="submit"
                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Save Changes
                                    </button>
                                    <button type="button"
                                            onclick="toggleEdit('college')"
                                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Department Details Panel --}}
                <div id="departmentDetails" class="hidden">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-white p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <i class="bx bx-building text-3xl text-red-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide">Department</div>
                                    <h2 id="departmentName" class="text-2xl font-bold text-gray-800 mt-1"></h2>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <i class="bx bxs-school text-green-600"></i>
                                        <span id="departmentCollegeName"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button"
                                    onclick="toggleEdit('department')"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center gap-2">
                                <i class="bx bx-edit"></i> Edit Department
                            </button>
                        </div>
                    </div>

                    {{-- View Mode --}}
                    <div id="departmentView" class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="text-sm text-gray-500 mb-1">Department Name</div>
                                    <div id="departmentNameView" class="text-lg font-semibold text-gray-800"></div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <div class="text-sm text-green-600 mb-1">Part of College</div>
                                    <div id="departmentCollegeNameView" class="text-lg font-semibold text-green-700"></div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                    <div class="text-sm text-purple-600 mb-1">Total Programs</div>
                                    <div id="departmentProgramCount" class="text-2xl font-bold text-purple-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Mode --}}
                    <div id="departmentEdit" class="hidden p-6">
                        <form id="departmentEditForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="departmentEditCollegeId" name="college_id">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
                                    <input type="text"
                                           id="departmentEditName"
                                           name="name"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                           required>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="submit"
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Save Changes
                                    </button>
                                    <button type="button"
                                            onclick="toggleEdit('department')"
                                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Program Details Panel --}}
                <div id="programDetails" class="hidden">
                    <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <i class="bx bx-book-open text-3xl text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 uppercase tracking-wide">Academic Program</div>
                                    <h2 id="programName" class="text-2xl font-bold text-gray-800 mt-1"></h2>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <i class="bx bx-building text-red-500"></i>
                                        <span id="programDepartmentName"></span>
                                        <span class="mx-2 text-gray-400">•</span>
                                        <i class="bx bxs-school text-green-600"></i>
                                        <span id="programCollegeName"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="button"
                                    onclick="toggleEdit('program')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2">
                                <i class="bx bx-edit"></i> Edit Program
                            </button>
                        </div>
                    </div>

                    {{-- View Mode --}}
                    <div id="programView" class="p-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="text-sm text-gray-500 mb-1">Program Name</div>
                                    <div id="programNameView" class="text-lg font-semibold text-gray-800"></div>
                                </div>
                            </div>
                            <div>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <div class="text-sm text-blue-600 mb-1">BOR Approval No.</div>
                                    <div id="programBorNo" class="text-lg font-semibold text-blue-700"></div>
                                </div>
                            </div>
                            <div>
                                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                                    <div class="text-sm text-indigo-600 mb-1">BOR Approval Date</div>
                                    <div id="programBorDate" class="text-lg font-semibold text-indigo-700"></div>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <div class="text-sm text-green-600 mb-1">Department</div>
                                    <div id="programDepartmentNameView" class="text-lg font-semibold text-green-700"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Mode --}}
                    <div id="programEdit" class="hidden p-6">
                        <form id="programEditForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="programEditDepartmentId" name="department_id">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Name</label>
                                    <input type="text"
                                           id="programEditName"
                                           name="name"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">BOR Approval Number</label>
                                    <input type="text"
                                           id="programEditBorNo"
                                           name="bor_approval_no"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">BOR Approval Date</label>
                                    <input type="date"
                                           id="programEditBorDate"
                                           name="bor_approval_date"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="submit"
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Save Changes
                                    </button>
                                    <button type="button"
                                            onclick="toggleEdit('program')"
                                            class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('AcademicStructure.modals.addCollegeModal')
@include('AcademicStructure.modals.addDepartmentModal')
@include('AcademicStructure.modals.addProgramModal')
@endsection

<script>
let currentItemType = null;
let currentItemId = null;

function openAddDepartmentModal(collegeId) {
    const input = document.getElementById('addDepartment_college_id');
    if (input) input.value = collegeId;
    const modal = document.getElementById('addDepartmentModal');
    if (modal) modal.showModal();
}

function openAddProgramModal(departmentId) {
    const input = document.getElementById('addProgram_department_id');
    if (input) input.value = departmentId;
    const modal = document.getElementById('addProgramModal');
    if (modal) modal.showModal();
}

function selectItem(type, id, name, parentId = null, parentName = null, grandParentId = null, grandParentName = null, borNo = null, borDate = null) {
    currentItemType = type;
    currentItemId = id;

    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('collegeDetails').classList.add('hidden');
    document.getElementById('departmentDetails').classList.add('hidden');
    document.getElementById('programDetails').classList.add('hidden');

    if (type === 'college') {
        const deptElements = document.querySelectorAll(`details[data-college-id="${id}"][data-department-id]`);
        const deptCount = deptElements.length;
        let programCount = 0;
        deptElements.forEach(dept => {
            programCount += dept.querySelectorAll('.program-item').length;
        });

        document.getElementById('collegeName').textContent = name;
        document.getElementById('collegeNameView').textContent = name;
        document.getElementById('collegeDeptCount').textContent = deptCount || '0';
        document.getElementById('collegeProgramCount').textContent = programCount || '0';
        document.getElementById('collegeEditName').value = name;
        document.getElementById('collegeEditForm').action = `/college/${id}`;
        document.getElementById('collegeDetails').classList.remove('hidden');
        document.getElementById('collegeView').classList.remove('hidden');
        document.getElementById('collegeEdit').classList.add('hidden');

    } else if (type === 'department') {
        const programCount = document.querySelectorAll(`.program-item[data-department-id="${id}"]`).length;

        document.getElementById('departmentName').textContent = name;
        document.getElementById('departmentNameView').textContent = name;
        document.getElementById('departmentCollegeName').textContent = parentName;
        document.getElementById('departmentCollegeNameView').textContent = parentName;
        document.getElementById('departmentProgramCount').textContent = programCount || '0';
        document.getElementById('departmentEditName').value = name;
        document.getElementById('departmentEditCollegeId').value = parentId;
        document.getElementById('departmentEditForm').action = `/department/${id}`;
        document.getElementById('departmentDetails').classList.remove('hidden');
        document.getElementById('departmentView').classList.remove('hidden');
        document.getElementById('departmentEdit').classList.add('hidden');

    } else if (type === 'program') {
        const formattedDate = new Date(borDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        document.getElementById('programName').textContent = name;
        document.getElementById('programNameView').textContent = name;
        document.getElementById('programDepartmentName').textContent = parentName;
        document.getElementById('programDepartmentNameView').textContent = parentName;
        document.getElementById('programCollegeName').textContent = grandParentName;
        document.getElementById('programBorNo').textContent = borNo;
        document.getElementById('programBorDate').textContent = formattedDate;
        document.getElementById('programEditName').value = name;
        document.getElementById('programEditBorNo').value = borNo;
        document.getElementById('programEditBorDate').value = borDate;
        document.getElementById('programEditDepartmentId').value = parentId;
        document.getElementById('programEditForm').action = `/program/${id}`;
        document.getElementById('programDetails').classList.remove('hidden');
        document.getElementById('programView').classList.remove('hidden');
        document.getElementById('programEdit').classList.add('hidden');
    }
}

function toggleEdit(type) {
    const viewEl = document.getElementById(`${type}View`);
    const editEl = document.getElementById(`${type}Edit`);

    if (viewEl && editEl) {
        viewEl.classList.toggle('hidden');
        editEl.classList.toggle('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const collegeAccordions = document.querySelectorAll('#collegeAccordions > details');

    collegeAccordions.forEach(details => {
        details.addEventListener('toggle', function() {
            if (this.open) {
                collegeAccordions.forEach(other => {
                    if (other !== this && other.open) {
                        other.open = false;
                    }
                });
            }
        });
    });

    collegeAccordions.forEach(college => {
        const departmentAccordions = college.querySelectorAll('div > details');
        departmentAccordions.forEach(details => {
            details.addEventListener('toggle', function() {
                if (this.open) {
                    departmentAccordions.forEach(other => {
                        if (other !== this && other.open) {
                            other.open = false;
                        }
                    });
                }
            });
        });
    });
});
</script>
