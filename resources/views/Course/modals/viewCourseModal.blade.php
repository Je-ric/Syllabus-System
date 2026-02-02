<x-modal.dialog id="viewCourseModal" maxWidth="max-w-3xl">
    <x-modal.header>
        <h3 class="text-xl font-semibold text-gray-800">Course Details</h3>
    </x-modal.header>

    <x-modal.body>
        <div id="modalContent" class="space-y-4">
            <div class="flex items-center justify-center py-8">
                <i class='bx bx-loader bx-spin text-4xl text-gray-400'></i>
            </div>
        </div>
    </x-modal.body>

    <x-modal.footer>
        <x-modal.close-button modalId="viewCourseModal" text="Close" />
    </x-modal.footer>
</x-modal.dialog>

<script>
    function viewCourseModal(courseId) {
        const modal = document.getElementById('viewCourseModal');
        const content = document.getElementById('modalContent');
        
        // Show modal immediately with loading state
        modal.showModal();
        
        // Fetch course data
        fetch(`/api/courses/${courseId}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch course');
                return response.json();
            })
            .then(data => {
                content.innerHTML = `
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Course Code</h4>
                            <p class="text-lg font-bold text-blue-600">${data.course_code}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Credit Units</h4>
                            <p class="text-lg font-semibold">${data.credit_units}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-600 text-sm mb-1">Course Title</h4>
                        <p class="text-lg font-semibold">${data.course_title}</p>
                    </div>

                    ${data.course_description ? `
                    <div>
                        <h4 class="font-semibold text-gray-600 text-sm mb-1">Description</h4>
                        <p class="text-gray-700">${data.course_description}</p>
                    </div>
                    ` : ''}

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Year Level</h4>
                            <p>${data.year_level ? 'Year ' + data.year_level : 'N/A'}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Semester</h4>
                            <p>${data.semester ? 'Semester ' + data.semester : 'N/A'}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Has Laboratory</h4>
                            <p>${data.has_lec_lab ? 'Yes' : 'No'}</p>
                        </div>
                    </div>

                    ${(data.prerequisite || data.corequisite) ? `
                    <div class="grid grid-cols-2 gap-4">
                        ${data.prerequisite ? `
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Prerequisite</h4>
                            <p class="text-gray-700">${data.prerequisite}</p>
                        </div>
                        ` : ''}
                        ${data.corequisite ? `
                        <div>
                            <h4 class="font-semibold text-gray-600 text-sm mb-1">Corequisite</h4>
                            <p class="text-gray-700">${data.corequisite}</p>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}

                    ${data.outcomes && data.outcomes.length > 0 ? `
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Program Outcomes Mapped</h4>
                        <div class="space-y-2">
                            ${data.outcomes.map(o => `
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                    <span class="font-semibold text-blue-600 min-w-[60px]">${o.po_code}</span>
                                    <span class="flex-1 text-sm text-gray-700">${o.po_text}</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded ${
                                        o.pivot.ied === 1 ? 'bg-green-100 text-green-700' : 
                                        o.pivot.ied === 2 ? 'bg-yellow-100 text-yellow-700' : 
                                        'bg-blue-100 text-blue-700'
                                    }">
                                        ${o.pivot.ied === 1 ? 'Introduced' : o.pivot.ied === 2 ? 'Emphasized' : 'Demonstrated'}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                `;
            })
            .catch(error => {
                console.error('Error fetching course data:', error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i class='bx bx-error-circle text-5xl text-red-400 mb-3'></i>
                        <p class="text-red-600 font-medium">Error loading course details</p>
                        <p class="text-sm text-gray-500 mt-2">${error.message}</p>
                    </div>
                `;
            });
    }
</script>