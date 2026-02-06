# Academic User & Role Management

This document describes the structure and relationships for user management, roles, and organizational assignments in the academic syllabus management system.

## Overview

In this system, user access and visibility are determined by **two separate concepts**:

1. **Roles (Permissions):** Define what actions a user can perform in the system (e.g., edit, approve, manage).
2. **Organizational Assignments (Scope):** Define which academic units (colleges, departments, programs) the user can see or manage.

> Roles control **what users can do**.
> Assignments control **what users can see**.

---

## Database Tables

### Roles

Stores all possible system roles:

| Column | Type   | Description                                             |
| ------ | ------ | ------------------------------------------------------- |
| id     | PK     | Role ID                                                 |
| name   | string | Role name (e.g., admin, faculty, chair, reviewer, dean) |

### User Roles

Maps users to roles (many-to-many):

| Column  | Type          | Description   |
| ------- | ------------- | ------------- |
| user_id | FK → users.id | The user      |
| role_id | FK → roles.id | Assigned role |

### User Assignments

Maps users to their academic unit for visibility/scope:

| Column        | Type                             | Description                           |
| ------------- | -------------------------------- | ------------------------------------- |
| id            | PK                               | Assignment ID                         |
| user_id       | FK → users.id                    | The user                              |
| college_id    | FK → colleges.id                 | College user belongs to (nullable)    |
| department_id | FK → departments.id              | Department user belongs to (nullable) |
| context       | ENUM('faculty', 'chair', 'dean') | Role context for the assignment       |

> Notes:
>
> * Faculty → typically assigned to a **department only**
> * Chair → assigned to **department**
> * Dean → assigned to **college only**
> * Users can have multiple assignments (e.g., faculty + reviewer)

---

### Colleges, Departments, Programs

| Table               | Key Columns                                  | Notes                                              |
| ------------------- | -------------------------------------------- | -------------------------------------------------- |
| colleges            | id, name                                     | Top-level academic unit                            |
| departments         | id, college_id, name                         | Department belongs to a college                    |
| programs            | id, name, bor_approval_no, bor_approval_date | Programs can belong to multiple departments        |
| program_departments | id, program_id, department_id, role          | Links programs to departments (primary/supporting) |

---

## Access Control Logic

| Role     | Scope Source                          | Permissions                                                |
| -------- | ------------------------------------- | ---------------------------------------------------------- |
| Faculty  | department_id (from user_assignments) | View programs & courses under their department             |
| Chair    | department_id                         | View faculty in department, review programs in department  |
| Dean     | college_id                            | View all departments, programs, and users in their college |
| Admin    | n/a                                   | Full access to all data and actions                        |
| Reviewer | assignment context (optional)         | Can approve/review assigned items                          |

> Visibility is **always determined by the assignment table**, never inferred from roles alone.

---

### Examples

**Faculty Assignment Example:**

| user_id | college_id | department_id | context |
| ------- | ---------- | ------------- | ------- |
| 12      | 3          | 7             | faculty |

* Can see programs/courses in department 7
* Cannot see other departments

**Dean Assignment Example:**

| user_id | college_id | department_id | context |
| ------- | ---------- | ------------- | ------- |
| 2       | 3          | NULL          | dean    |

* Can see all departments and users under college 3

---

## Best Practices

1. **Roles = permissions**; **Assignments = visibility**
2. Never encode department/college in the role name (e.g., `faculty_math`)
3. Support multiple assignments for users with overlapping responsibilities
4. Use policies (e.g., Laravel Gates) to enforce both **role permissions** and **assignment scope**
5. Separate **organization hierarchy** from **system permissions** for maintainability

---

## Business Rules & Conditions

### Account Approval Controller — Role Assignment Workflow

When an administrator assigns or modifies a user's roles through the approval system:

**Pre-Conditions (Requirements that must be met):**
* The user account must have an active status in the system
* The user must exist in the database
* The roles being assigned must be valid system roles (admin, chair, dean, or faculty)

**Automatic Behavior:**
* Faculty role is always included—it cannot be removed from a user, it is mandatory
* Any user assigned as dean or chair is automatically given the faculty role as a foundation

**Role Removal Conditions:**
* When a dean role is removed from a user's role list, all dean assignment records for that user are immediately deleted from the system
* When a chair role is removed from a user's role list, all chair assignment records for that user are immediately deleted from the system
* **Important**: Removing dean or chair roles does NOT remove the faculty role or faculty assignments—those always remain

**Example Scenario:**
If a user was promoted to dean of a college and faculty member of a department, then an administrator removes the dean role, the dean record is deleted but the faculty status and department assignment are preserved.

---

### Organizational Hierarchy Controller — Dean & Chair Assignment Workflow

When assigning a dean or chair to an academic unit:

**Pre-Conditions for Dean Assignment:**
* The selected college must exist in the system
* The selected user must exist in the system
* The user must already have the dean role OR have administrator authority
* The user must NOT be assigned as a chair anywhere in the system (conflict prevention)
* The user must NOT already be a dean of the selected college (no duplicate assignments)

**Pre-Conditions for Chair Assignment:**
* The selected department must exist in the system
* The selected user must exist in the system
* The user must already have the chair role OR have administrator authority
* The user must NOT be assigned as a dean anywhere in the system (conflict prevention)
* The user must NOT already be a chair of the selected department (no duplicate assignments)

**Automatic Creation When Assigning Dean:**
* A dean assignment record is created linking the user to the college with context "dean"
* The faculty role is automatically assigned to the user if not already present
* A faculty assignment record is automatically created linking the user to the college with context "faculty"

**Automatic Creation When Assigning Chair:**
* A chair assignment record is created linking the user to the department with context "chair"
* The faculty role is automatically assigned to the user if not already present
* A faculty assignment record is automatically created linking the user to the department with context "faculty"

**Multiple Assignments:**
* A single user can be a dean of multiple colleges simultaneously
* A single user can be a chair of multiple departments simultaneously
* However, a user cannot hold both dean and chair roles at the same time (mutual exclusivity)

---

### Summary

* **Roles**: What users can do (edit, approve, manage)
* **User Assignments**: What users can see (college, department, program)
* **Faculty → Department**
* **Chair → Department**
* **Dean → College**
* **Admin → Full access**

This structure ensures flexibility, scalability, and clear separation of permission vs. scope logic.

---
