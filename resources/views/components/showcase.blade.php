{{-- resources/views/components/showcase.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="p-6 md:p-10 space-y-14 max-w-6xl mx-auto">

    {{-- ─── Page Header ─────────────────────────────────────────────────── --}}
    <div class="border-b border-[#E3E8EB] pb-6">
        <div class="flex items-center gap-2 mb-1.5">
            <div class="w-2 h-2 rounded-full bg-[#00C075]"></div>
            <span class="text-[11px] font-semibold tracking-widest uppercase text-[#72809E]">Design System</span>
        </div>
        <h1 class="text-3xl font-bold text-[#394056] tracking-tight">UI Component Showcase</h1>
        <p class="mt-1 text-sm text-[#72809E]">Visual reference for all reusable UI primitives. Hover elements to see state transitions.</p>
    </div>


    {{-- ─── 1. Buttons ─────────────────────────────────────────────────── --}}
    <section class="space-y-5">

        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">1. Buttons</h2>
            <x-ui.code-badge variant="grey">x-ui.button</x-ui.code-badge>
        </div>

        {{-- Form / CRUD --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Form / CRUD — Gradient fills, visible on white</p>
            <div class="flex flex-wrap gap-4">
                @foreach ([
                    'primary'    => 'bx bx-check',
                    'save'       => 'bx bx-save',
                    'add-button' => 'bx bx-plus',
                    'secondary'  => 'bx bx-cog',
                    'outline'    => 'bx bx-link',
                    'add-dashed' => 'bx bx-plus-circle',
                    'cancel'     => 'bx bx-x',
                    'back'       => 'bx bx-arrow-back',
                    'danger'     => 'bx bx-trash',
                    'warning'    => 'bx bx-error',
                    'gold'       => 'bx bx-star',
                ] as $v => $ico)
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.button variant="{{ $v }}">
                            <i class="{{ $ico }}"></i> {{ ucwords(str_replace('-', ' ', $v)) }}
                        </x-ui.button>
                        <span class="font-mono text-[9.5px] text-[#B4C0CA]">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Table actions --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Table Actions — Solid dark fills</p>
            <div class="flex flex-wrap gap-3">
                @foreach ([
                    'table-confirm'  => 'bx bx-check',
                    'table-edit'     => 'bx bx-pencil',
                    'table-view'     => 'bx bx-show',
                    'table-manage'   => 'bx bx-cog',
                    'table-danger'   => 'bx bx-trash',
                    'table-disable'  => 'bx bx-pause',
                    'table-restore'  => 'bx bx-undo',
                    'table-cancel'   => 'bx bx-x',
                ] as $v => $ico)
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.button variant="{{ $v }}">
                            <i class="{{ $ico }}"></i> {{ ucwords(str_replace(['table-','-'], ['', ' '], $v)) }}
                        </x-ui.button>
                        <span class="font-mono text-[9.5px] text-[#B4C0CA]">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Small / Wizard --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Small / Wizard — Pills</p>
            <div class="flex flex-wrap gap-3">
                @foreach (['sm-primary','sm-cancel','sm-danger','sm-warning','sm-info','sm-success','sm-soft','sm-add'] as $v)
                    <div class="flex flex-col items-center gap-1.5">
                        <x-ui.button variant="{{ $v }}">
                            {{ ucwords(str_replace(['sm-','-'], ['', ' '], $v)) }}
                        </x-ui.button>
                        <span class="font-mono text-[9.5px] text-[#B4C0CA]">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Loading + Link --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Loading State &amp; Link Button</p>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="flex flex-col items-center gap-1.5">
                    <x-ui.button variant="primary" type="button" loading="Processing…">Submit</x-ui.button>
                    <span class="font-mono text-[9.5px] text-[#B4C0CA]">loading="Processing…"</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                    <x-ui.button variant="danger" type="button" loading="Deleting…">Delete</x-ui.button>
                    <span class="font-mono text-[9.5px] text-[#B4C0CA]">loading="Deleting…"</span>
                </div>
                <div class="flex flex-col items-center gap-1.5">
                    <x-ui.button variant="outline" href="#">
                        <i class="bx bx-link-external"></i> Link Button
                    </x-ui.button>
                    <span class="font-mono text-[9.5px] text-[#B4C0CA]">href="#"</span>
                </div>
            </div>
        </div>
    </section>


    {{-- ─── 2. Code Badge ───────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">2. Code Badge</h2>
            <x-ui.code-badge variant="grey">x-ui.code-badge</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Light bg · text = border color</p>
            <div class="flex flex-wrap gap-4 items-center">
                @foreach (['emerald','blue','amber','red','grey'] as $v)
                    <div class="flex flex-col items-center gap-2">
                        <x-ui.code-badge variant="{{ $v }}">{{ strtoupper($v) }}_CODE</x-ui.code-badge>
                        <span class="font-mono text-[9.5px] text-[#B4C0CA]">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-[#F1F3F5] flex flex-wrap gap-2 items-center">
                <span class="text-xs text-[#72809E] mr-1">Usage:</span>
                <x-ui.code-badge>CS 101</x-ui.code-badge>
                <x-ui.code-badge variant="blue">SECTION A</x-ui.code-badge>
                <x-ui.code-badge variant="amber">AY 2024–25</x-ui.code-badge>
                <x-ui.code-badge variant="red">ARCHIVED</x-ui.code-badge>
                <x-ui.code-badge variant="grey">DRAFT</x-ui.code-badge>
            </div>
        </div>
    </section>


    {{-- ─── 3. Status Indicator ────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">3. Status Indicator</h2>
            <x-ui.code-badge variant="grey">x-feedback-status.status-indicator</x-ui.code-badge>
        </div>

        {{-- Semantic statuses --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Semantic Statuses — with icon (auto-resolved)</p>
            <div class="flex flex-wrap gap-2">
                @foreach (['success','active','info','warning','pending','danger','rejected','neutral','disabled'] as $s)
                    <x-feedback-status.status-indicator status="{{ $s }}" />
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
                <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">With dot indicator</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (['success','warning','danger','neutral','info'] as $s)
                        <x-feedback-status.status-indicator status="{{ $s }}" :dot="true" />
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
                <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">Sizes — sm · md · lg</p>
                <div class="flex flex-wrap items-center gap-3">
                    <x-feedback-status.status-indicator status="active" size="sm" />
                    <x-feedback-status.status-indicator status="active" size="md" />
                    <x-feedback-status.status-indicator status="active" size="lg" />
                </div>
            </div>
        </div>

        {{-- Role badges --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Role Badges</p>
            <div class="flex flex-wrap gap-2">
                @foreach (['admin','dean','chair','faculty'] as $s)
                    <x-feedback-status.status-indicator status="{{ $s }}" />
                @endforeach
            </div>
        </div>

        {{-- Course type badges --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Course Type Badges</p>
            <div class="flex flex-wrap gap-2">
                <x-feedback-status.status-indicator status="lec" label="Lecture" />
                <x-feedback-status.status-indicator status="lec_lab" label="Lec + Lab" />
            </div>
        </div>

        {{-- Direct variant access --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Direct Variant (custom label)</p>
            <div class="flex flex-wrap gap-2">
                @foreach (['brand','emerald','blue','lab','sky','amber','rose','violet','indigo','slate'] as $v)
                    <x-feedback-status.status-indicator variant="{{ $v }}" label="{{ ucfirst($v) }}" />
                @endforeach
            </div>
        </div>
    </section>


    {{-- ─── 4. Help Trigger ─────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">4. Help Trigger</h2>
            <x-ui.code-badge variant="grey">x-ui.help-trigger</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-xs text-[#72809E] mb-4">
                Dispatches <code class="bg-[#F1F3F5] text-[#394056] px-1.5 py-0.5 rounded text-[11px] font-mono">open-help-panel</code>
                on click. Hover for emerald tint transition.
            </p>
            <x-ui.help-trigger />
        </div>
    </section>


    {{-- ─── 5. Text Block ───────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">5. Text Block</h2>
            <x-ui.code-badge variant="grey">x-ui.text-block</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-xs text-[#72809E] mb-4">Hover each block — shadow lifts, border shifts to Emerald 200.</p>
            <div class="space-y-3">
                <x-ui.text-block>
                    <i class="bx bx-info-circle text-[#00C075] text-base shrink-0 mt-0.5"></i>
                    <span>This course is currently <strong class="text-[#394056]">active</strong> and visible to enrolled students.</span>
                </x-ui.text-block>
                <x-ui.text-block>
                    <i class="bx bx-lock text-[#3197D6] text-base shrink-0 mt-0.5"></i>
                    <span>Enrollment is <strong class="text-[#394056]">closed</strong>. Contact the registrar to make changes.</span>
                </x-ui.text-block>
                <x-ui.text-block>
                    <i class="bx bx-error text-[#F5B126] text-base shrink-0 mt-0.5"></i>
                    <span>Prerequisite requirements have <strong class="text-[#394056]">not been met</strong> for this section.</span>
                </x-ui.text-block>
            </div>
        </div>
    </section>


    {{-- ─── 6. User Row ─────────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">6. User Row</h2>
            <x-ui.code-badge variant="grey">x-ui.user-row</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">

            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">Read-only</p>
            @php
                $demoUsers = [
                    (object)['name' => 'Maria Santos',   'email' => 'maria.santos@clsu.edu.ph'],
                    (object)['name' => 'Juan dela Cruz', 'email' => 'jdelacruz@clsu.edu.ph'],
                ];
            @endphp
            <div class="space-y-2 mb-6">
                @foreach ($demoUsers as $u)
                    <div class="px-3 py-2 rounded-[7px] bg-[#F9FAFA] border border-[#F1F3F5]">
                        <x-ui.user-row :user="$u" />
                    </div>
                @endforeach
            </div>

            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">With Remove Action</p>
            <div class="space-y-2">
                @foreach ($demoUsers as $i => $u)
                    <div class="px-3 py-2 rounded-[7px] bg-[#F9FAFA] border border-[#F1F3F5]">
                        <x-ui.user-row
                            :user="$u"
                            :canRemove="true"
                            removeModalId="demo-remove-modal-{{ $i }}"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ─── 7. Layout Components ───────────────────────────────────────── --}}
    <section class="space-y-5">

        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">7. Layout Components</h2>
            <x-ui.code-badge variant="grey">x-layout.*</x-ui.code-badge>
        </div>

        {{-- page-header --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] overflow-hidden
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <div class="px-4 py-2.5 border-b border-[#F1F3F5] bg-[#F9FAFA]">
                <span class="font-mono text-[10.5px] text-[#93A1AF]">x-layout.page-header</span>
            </div>
            <x-layout.page-header
                icon="bx-book-open"
                title="Course Management"
                desc="Manage syllabi, sections, and enrolled faculty for this semester.">
                <x-ui.button variant="primary"><i class="bx bx-plus"></i> Add Course</x-ui.button>
                <x-ui.help-trigger />
            </x-layout.page-header>
            {{-- Without icon --}}
            <x-layout.page-header title="Plain Header (no icon)" class="border-t border-[#F1F3F5]" />
        </div>

        {{-- card (all color variants) --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">x-layout.card — color variants</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach (['slate','emerald','blue','amber','rose','violet','navy','gold'] as $c)
                    <x-layout.card color="{{ $c }}" title="{{ ucfirst($c) }} Card" icon="layer">
                        <p class="text-xs text-[#72809E]">Sample card body content for the <strong class="text-[#394056]">{{ $c }}</strong> variant.</p>
                    </x-layout.card>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
                <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">With action slot</p>
                <x-layout.card color="emerald" title="With Action" icon="cog">
                    <x-slot name="action">
                        <x-ui.button variant="sm-primary"><i class="bx bx-pencil"></i> Edit</x-ui.button>
                    </x-slot>
                    <p class="text-xs text-[#72809E]">Card body content here.</p>
                </x-layout.card>
            </div>
        </div>

        {{-- card-section --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">x-layout.card-section</p>
            <div class="space-y-4">
                <x-layout.card-section title="Faculty Members" icon="bx bx-group" :count="3">
                    <x-slot name="actions">
                        <x-ui.button variant="sm-add"><i class="bx bx-plus"></i> Add</x-ui.button>
                    </x-slot>
                    <p class="text-xs text-[#72809E]">List of assigned faculty would appear here.</p>
                </x-layout.card-section>
                <x-layout.card-section title="Enrolled Sections" icon="bx bx-calendar" subtitle="AY 2024–25, Sem 1" :count="12">
                    <p class="text-xs text-[#72809E]">Section entries would appear here.</p>
                </x-layout.card-section>
            </div>
        </div>

        {{-- section-heading --}}
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">x-layout.section-heading</p>
            <x-layout.section-heading number="1" label="Basic Information" />
            <p class="text-xs text-[#72809E] mb-4">Form fields for this section would go here.</p>
            <x-layout.section-heading number="2" label="Course Details" />
            <p class="text-xs text-[#72809E] mb-4">More form fields here.</p>
            <x-layout.section-heading number="3" label="Assignments &amp; Grading" />
            <p class="text-xs text-[#72809E]">Grading configuration here.</p>
        </div>

    </section>


    {{-- ─── 8. Alert ──────────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">8. Alert</h2>
            <x-ui.code-badge variant="grey">x-feedback-status.alert</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <div class="space-y-3">
                <x-feedback-status.alert type="success" message="Course syllabus has been saved successfully." />
                <x-feedback-status.alert type="info" message="This section is currently open for enrollment." />
                <x-feedback-status.alert type="warning" message="Prerequisite mapping is incomplete for 2 outcomes." />
                <x-feedback-status.alert type="error" message="Failed to save. Please check all required fields." />
                <x-feedback-status.alert type="success" :dismissable="true"
                    message="Your changes have been submitted for review." />
                <x-feedback-status.alert type="info" :showTitle="false"
                    message="Inline notice with no title — compact and minimal." />
            </div>
        </div>
    </section>


    {{-- ─── 9. IED Badge ──────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">9. IED Badge</h2>
            <x-ui.code-badge variant="grey">x-feedback-status.ied-badge</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">
                Light bg · text = border — I / E / D / fallback
            </p>
            <div class="flex items-center gap-4">
                @foreach (['I', 'E', 'D', '–'] as $lvl)
                    <div class="flex flex-col items-center gap-2">
                        <x-feedback-status.ied-badge :level="$lvl" />
                        <span class="font-mono text-[9.5px] text-[#B4C0CA]">
                            {{ $lvl === 'I' ? 'Introductory' : ($lvl === 'E' ? 'Enabling' : ($lvl === 'D' ? 'Demonstrating' : 'None')) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-[#F1F3F5]">
                <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-3">In a table context</p>
                <x-table.container>
                    <x-table.table>
                        <x-table.head>
                            <tr>
                                <x-table.th>Outcome</x-table.th>
                                <x-table.th align="center">CO 1</x-table.th>
                                <x-table.th align="center">CO 2</x-table.th>
                                <x-table.th align="center">CO 3</x-table.th>
                            </tr>
                        </x-table.head>
                        <x-table.body>
                            <x-table.row :hover="true">
                                <x-table.td>Problem Solving</x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="I" /></x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="E" /></x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="D" /></x-table.td>
                            </x-table.row>
                            <x-table.row :hover="true">
                                <x-table.td>Communication</x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="–" /></x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="I" /></x-table.td>
                                <x-table.td align="center"><x-feedback-status.ied-badge level="E" /></x-table.td>
                            </x-table.row>
                        </x-table.body>
                    </x-table.table>
                </x-table.container>
            </div>
        </div>
    </section>


    {{-- ─── 10. Accordion ─────────────────────────────────────────────── --}}
    <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">10. Accordion</h2>
            <x-ui.code-badge variant="grey">x-layout.accordion</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Color variants</p>
            <div class="space-y-3">
                <x-layout.accordion title="Course Outcomes" icon="target" color="emerald" :open="true"
                    badge="3 mapped" badgeVariant="brand">
                    <p class="text-sm text-[#72809E]">Expanded by default. Body content goes here.</p>
                </x-layout.accordion>

                <x-layout.accordion title="Assigned Sections" icon="calendar" color="blue">
                    <x-slot name="actions">
                        <x-ui.button variant="sm-add"><i class="bx bx-plus"></i> Add</x-ui.button>
                    </x-slot>
                    <p class="text-sm text-[#72809E]">Collapsed by default. Click to expand.</p>
                </x-layout.accordion>

                <x-layout.accordion title="Pending Requirements" icon="time" color="amber" badge="2 pending" badgeVariant="amber">
                    <p class="text-sm text-[#72809E]">Warning-toned accordion with a badge.</p>
                </x-layout.accordion>

                <x-layout.accordion title="Archived Records" icon="archive" color="rose">
                    <p class="text-sm text-[#72809E]">Rose/danger-toned for destructive or archived content.</p>
                </x-layout.accordion>

                <x-layout.accordion title="Plain (no icon, slate)" color="slate">
                    <p class="text-sm text-[#72809E]">Default slate color, no icon.</p>
                </x-layout.accordion>
            </div>
        </div>
    </section>


    {{-- ─── 11. Table ─────────────────────────────────────────────────── --}}
    {{-- <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">11. Table</h2>
            <x-ui.code-badge variant="grey">x-table.*</x-ui.code-badge>
        </div>

        Standard table
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Standard with hover rows</p>
            <x-table.container>
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>#</x-table.th>
                            <x-table.th>Course</x-table.th>
                            <x-table.th>Section</x-table.th>
                            <x-table.th>Status</x-table.th>
                            <x-table.th align="center">Units</x-table.th>
                            <x-table.th align="right">Actions</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @php
                            $rows = [
                                ['CS 101', 'A', 'active',  3],
                                ['CS 202', 'B', 'pending', 3],
                                ['CS 303', 'A', 'danger',  2],
                            ];
                        @endphp
                        @foreach ($rows as $i => $r)
                            <x-table.row :hover="true">
                                <x-table.td class="text-[#93A1AF] font-mono">{{ $i + 1 }}</x-table.td>
                                <x-table.td class="font-semibold text-[#394056]">
                                    <x-ui.code-badge>{{ $r[0] }}</x-ui.code-badge>
                                </x-table.td>
                                <x-table.td>Section {{ $r[1] }}</x-table.td>
                                <x-table.td>
                                    <x-feedback-status.status-indicator status="{{ $r[2] }}" />
                                </x-table.td>
                                <x-table.td align="center">{{ $r[3] }}</x-table.td>
                                <x-table.td align="right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button variant="table-view"><i class="bx bx-show"></i> View</x-ui.button>
                                        <x-ui.button variant="table-edit"><i class="bx bx-pencil"></i> Edit</x-ui.button>
                                        <x-ui.button variant="table-danger"><i class="bx bx-trash"></i></x-ui.button>
                                    </div>
                                </x-table.td>
                            </x-table.row>
                        @endforeach
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </div>

        Striped table
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Striped rows</p>
            <x-table.container>
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>Faculty</x-table.th>
                            <x-table.th>Email</x-table.th>
                            <x-table.th>Role</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        @php
                            $faculty = [
                                ['Dr. Maria Santos',   'msantos@clsu.edu.ph',   'dean'],
                                ['Prof. Juan Cruz',    'jcruz@clsu.edu.ph',     'chair'],
                                ['Ms. Ana Reyes',      'areyes@clsu.edu.ph',    'faculty'],
                                ['Mr. Rico Dela Cruz', 'rdelacruz@clsu.edu.ph', 'faculty'],
                            ];
                        @endphp
                        @foreach ($faculty as $f)
                            <x-table.row :striped="true">
                                <x-table.td class="font-medium text-[#394056]">{{ $f[0] }}</x-table.td>
                                <x-table.td class="text-[#72809E]">{{ $f[1] }}</x-table.td>
                                <x-table.td>
                                    <x-feedback-status.status-indicator status="{{ $f[2] }}" />
                                </x-table.td>
                            </x-table.row>
                        @endforeach
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </div>

        Empty state
        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]">
            <p class="text-[10.5px] font-semibold uppercase tracking-widest text-[#93A1AF] mb-4">Empty state</p>
            <x-table.container>
                <x-table.table>
                    <x-table.head>
                        <tr>
                            <x-table.th>Course</x-table.th>
                            <x-table.th>Status</x-table.th>
                            <x-table.th align="right">Actions</x-table.th>
                        </tr>
                    </x-table.head>
                    <x-table.body>
                        <x-table.empty :colspan="3" message="No courses found for this semester." />
                    </x-table.body>
                </x-table.table>
            </x-table.container>
        </div>
    </section> --}}


    {{-- ─── 12. Offcanvas ─────────────────────────────────────────────── --}}
    {{-- <section class="space-y-4">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-bold text-[#394056]">12. Offcanvas</h2>
            <x-ui.code-badge variant="grey">x-layout.offcanvas</x-ui.code-badge>
        </div>

        <div class="bg-white border border-[#E3E8EB] rounded-[12px] p-6
                    shadow-[0_1px_2px_rgba(16,24,40,0.04),0_1px_3px_rgba(16,24,40,0.06)]"
             x-data="{ panelOpen: false }">
            <p class="text-xs text-[#72809E] mb-4">
                Click the button to open the offcanvas panel. Uses backdrop blur + directional slide animation.
            </p>
            <div class="flex gap-3">
                <x-ui.button variant="primary" x-on:click="panelOpen = true">
                    <i class="bx bx-layout"></i> Open Offcanvas
                </x-ui.button>
            </div>

            <x-layout.offcanvas
                title="Course Details"
                subtitle="AY 2024–25 · Semester 1"
                icon="bx bx-book-open"
                open="panelOpen">
                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-2">
                        <x-ui.button variant="cancel" x-on:click="panelOpen = false">Cancel</x-ui.button>
                        <x-ui.button variant="save"><i class="bx bx-save"></i> Save Changes</x-ui.button>
                    </div>
                </x-slot>
                <div class="space-y-4">
                    <x-feedback-status.alert type="info" :showTitle="false"
                        message="Review the details below before saving." />
                    <x-layout.section-heading number="1" label="Course Information" />
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-[#72809E] uppercase tracking-wider">Course Code</p>
                        <p class="text-sm font-semibold text-[#394056]">CS 101</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-[#72809E] uppercase tracking-wider">Description</p>
                        <p class="text-sm text-[#394056]">Introduction to Computer Science fundamentals.</p>
                    </div>
                    <x-layout.section-heading number="2" label="Status" />
                    <div class="flex items-center gap-2">
                        <x-feedback-status.status-indicator status="active" size="lg" />
                        <span class="text-sm text-[#72809E]">Currently active and visible to students.</span>
                    </div>
                </div>
            </x-layout.offcanvas>
        </div>
    </section> --}}

</div>
@endsection
