@if ($data['no_assignment'])
    <x-feedback-status.alert type="warning" title="No college assigned"
        message="You have the Dean role but are not assigned to any college. Contact an administrator to be assigned." />
@else
    <x-dashboard.bento-grid
        :data="$data"
        :user="$user"
        ied-empty-message="All courses in your college have IED mappings configured."
        syllabi-empty-message="No syllabi have been created or updated recently in your college."
    />
@endif
