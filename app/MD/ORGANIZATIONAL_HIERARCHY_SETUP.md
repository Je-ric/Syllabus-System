# Organizational Hierarchy & User Assignment System

## Overview

This system provides a complete solution for managing deans per college and chairs per department, with hierarchical visibility for viewing organizational structure.

## Components Created

### 1. **Controller: OrganizationalHierarchyController**
Location: `app/Http/Controllers/OrganizationalHierarchyController.php`

**Key Methods:**
- `collegesIndex()` - Display all colleges with dean assignments
- `assignDean()` - Assign a dean to a college
- `removeDean()` - Remove dean from college
- `departmentsIndex()` - Display departments in a college with chair assignments
- `assignChair()` - Assign a chair to a department
- `removeChair()` - Remove chair from department
- `hierarchyView()` - Display hierarchical view (Dean sees college > departments > chairs > faculty; Chair sees department > faculty)

### 2. **Blade Views**

#### Admin Interface
- **`resources/views/OrganizationalHierarchy/colleges.blade.php`**
  - Grid view of all colleges
  - Shows current dean assignment
  - Buttons to assign/remove deans
  - Shows departments overview
  - Modals for dean assignment

- **`resources/views/OrganizationalHierarchy/departments.blade.php`**
  - List of departments in a college
  - Shows current chair assignment
  - Lists faculty under each chair
  - Buttons to assign/remove chairs
  - Modals for chair assignment

#### User Role Views (Dean/Chair)
- **`resources/views/OrganizationalHierarchy/hierarchy-dean.blade.php`**
  - Dean sees their college name
  - All departments in the college
  - Department chairs
  - Faculty under each chair
  - Summary statistics

- **`resources/views/OrganizationalHierarchy/hierarchy-chair.blade.php`**
  - Chair sees their department
  - List of faculty members with details
  - Department and college information
  - Summary statistics

- **`resources/views/OrganizationalHierarchy/no-assignment.blade.php`**
  - Shown when user has no dean/chair assignment

### 3. **Model Updates**

#### College Model
Added method:
```php
public function deanAssignment()
{
    return UserAssignment::where('college_id', $this->id)
        ->where('context', 'dean')
        ->with('user')
        ->first();
}
```

#### Department Model
Added method:
```php
public function deptChair()
{
    $assignment = UserAssignment::where('department_id', $this->id)
        ->where('context', 'chair')
        ->with('user')
        ->first();
    
    return $assignment ? $assignment->user : null;
}
```

### 4. **Routes** (Added to `routes/web.php`)

**Admin Routes** (Protected with `middleware(['role:admin'])`)
```
GET  /organizational/colleges                           → collegesIndex()
POST /organizational/assign-dean                        → assignDean()
POST /organizational/remove-dean                        → removeDean()
GET  /organizational/college/{collegeId}/departments    → departmentsIndex()
POST /organizational/assign-chair                       → assignChair()
POST /organizational/remove-chair                       → removeChair()
```

**User Routes** (Protected with `middleware(['role:admin,dean,chair'])`)
```
GET  /organizational/hierarchy                          → hierarchyView()
```

---

## User Assignment Database Schema

The existing `user_assignments` table tracks organizational scope:

| Column | Type | Description |
|--------|------|-------------|
| id | PK | Auto-increment ID |
| user_id | FK | Reference to users table |
| college_id | FK (nullable) | College assignment (only for deans) |
| department_id | FK (nullable) | Department assignment (for chairs & faculty) |
| context | ENUM | One of: `faculty`, `chair`, `dean` |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Update timestamp |

**Constraints:**
- Unique constraint on (user_id, college_id, department_id, context)
- One user can only be ONE dean per college
- One user can only be ONE chair per department
- Multiple faculty can be in one department

---

## Usage Flow

### For Admins

1. **Assign Dean to College:**
   - Navigate to `/organizational/colleges`
   - Find the college
   - Click "Assign Dean" button
   - Select a user with "dean" role
   - Confirm in modal

2. **Assign Chair to Department:**
   - From college cards, click "Manage Departments"
   - Find the department
   - Click "Assign Chair" button
   - Select a user with "chair" role
   - Confirm in modal

### For Deans

1. View their college and all departments
2. See assigned chairs for each department
3. See all faculty under each chair
4. View summary statistics

Access via: `/organizational/hierarchy`

### For Chairs

1. View their department
2. See list of faculty members
3. View faculty contact details
4. View summary statistics

Access via: `/organizational/hierarchy`

---

## Role Requirements

**To Assign Dean:**
- User must have `dean` or `admin` role

**To Assign Chair:**
- User must have `chair` or `admin` role

**To View Hierarchy:**
- User must have `admin`, `dean`, or `chair` role

---

## Data Assignment Examples

### Creating a Dean Assignment
```php
UserAssignment::create([
    'user_id' => 5,
    'college_id' => 1,
    'department_id' => null,
    'context' => 'dean',
]);
```

### Creating a Chair Assignment
```php
UserAssignment::create([
    'user_id' => 8,
    'college_id' => null,
    'department_id' => 3,
    'context' => 'chair',
]);
```

### Creating a Faculty Assignment
```php
UserAssignment::create([
    'user_id' => 15,
    'college_id' => null,
    'department_id' => 3,
    'context' => 'faculty',
]);
```

---

## Testing

After running migrations and seeding:

1. **Create test users** with different roles (admin, dean, chair, faculty)
2. **Test Admin Interface:**
   - Route: `/organizational/colleges`
   - Assign a dean to a college
   - Go to manage departments and assign chairs

3. **Test Dean View:**
   - Login as dean user
   - Route: `/organizational/hierarchy`
   - Verify you see your college and all departments

4. **Test Chair View:**
   - Login as chair user
   - Route: `/organizational/hierarchy`
   - Verify you see your department and faculty

---

## Key Features

✅ **Admin Control**: Full management of deans and chairs
✅ **Hierarchical Visibility**: Deans see their college structure; Chairs see their department
✅ **Responsive Design**: Works on desktop and mobile with daisyUI components
✅ **Form Validation**: Ensures only users with required roles are assigned
✅ **Toast Notifications**: User-friendly feedback on actions
✅ **Prevents Duplicates**: Only one dean per college, one chair per department
✅ **Cascading Deletes**: Safe deletion with proper database constraints

---

## Integration Notes

- Uses existing `UserAssignment` model with relational structure
- Compatible with current role-based access control system
- Follows Laravel conventions and Tailwind/daisyUI styling
- Toast messages use your existing toast component
- No breaking changes to existing code

---

## Future Enhancements

- CSV export of organizational structure
- Bulk assignment operations
- Assignment history tracking
- Dashboard statistics for deans/chairs
- Email notifications on assignment changes
