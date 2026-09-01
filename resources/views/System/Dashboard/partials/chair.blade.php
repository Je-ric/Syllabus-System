@if ($data['no_assignment'])
    <x-feedback-status.alert type="warning" title="No department assigned"
        message="You have the Chairperson role but are not assigned to any department. Contact an administrator to be assigned." />
@else
    <x-dashboard.bento-grid
        :data="$data"
        :user="$user"
        ied-empty-message="All courses in your department have IED mappings configured."
        syllabi-empty-message="No syllabi have been created or updated recently in your department."
    />
@endif
