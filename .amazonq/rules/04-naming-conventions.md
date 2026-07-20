# Naming Conventions

## Variables & Functions

| Context | Convention | Example |
|---|---|---|
| PHP variables | camelCase | `$userId`, `$programName` |
| JS/TS variables | camelCase | `userId`, `isLoading` |
| PHP functions | camelCase | `getUserById()` |
| JS functions | camelCase | `handleSubmit()` |
| Constants | UPPER_SNAKE_CASE | `MAX_FILE_SIZE`, `DEFAULT_ROLE` |
| Boolean variables | prefix with is/has/can | `isActive`, `hasRole`, `canEdit` |

---

## Database

| Item | Convention | Example |
|---|---|---|
| Tables | plural_snake_case | `program_volunteers`, `audit_logs` |
| Columns | snake_case | `first_name`, `created_at` |
| Primary key | `id` | `id` |
| Foreign key | `{table_singular}_id` | `user_id`, `program_id` |
| Pivot tables | alphabetical singular | `program_user`, `role_user` |
| Soft delete column | `deleted_at` | `deleted_at` |
| Boolean columns | prefix with `is_` | `is_active`, `is_verified` |

---

## Laravel (PHP)

| Item | Convention | Example |
|---|---|---|
| Models | SingularPascalCase | `User`, `ProgramVolunteer` |
| Controllers | PascalCase + Controller | `UserController` |
| Services | PascalCase + Service | `AuditLogService` |
| Requests | PascalCase + Request | `StoreUserRequest` |
| Events | PascalCase (past tense) | `UserRoleUpdated` |
| Listeners | PascalCase | `SendRoleUpdateNotification` |
| Notifications | PascalCase | `PaymentReminder` |
| Migrations | snake_case descriptive | `create_users_table`, `add_status_to_users_table` |
| Seeders | PascalCase + Seeder | `UserSeeder` |
| Factories | PascalCase + Factory | `UserFactory` |

---

## Routes

| Type | Convention | Example |
|---|---|---|
| Web routes | kebab-case | `/program-volunteers`, `/audit-logs` |
| Route names | dot.notation | `users.index`, `programs.store` |
| API routes | kebab-case, versioned | `/api/v1/users` |

---

## Frontend

| Item | Convention | Example |
|---|---|---|
| Vue/React components | PascalCase | `UserTable`, `ConfirmModal` |
| Blade views | snake_case or kebab | `user_table.blade.php` |
| CSS classes | kebab-case | `btn-primary`, `card-header` |
| JS files | kebab-case | `user-table.js` |
| Vue files | PascalCase | `UserTable.vue` |

---

## Design Tokens & CSS

| Item | Convention | Example |
|---|---|---|
| CSS custom properties (tokens) | kebab-case, prefixed by purpose | `--color-primary`, `--shadow-elevated` |
| Utility/label text classes | consistent scale, not one-off values | `text-[10px] font-bold uppercase tracking-[0.14em]` for section labels |
| Component-scoped classes | kebab-case | `card-panel`, `stat-badge` |

---

## Files & Folders

| Item | Convention | Example |
|---|---|---|
| Laravel controllers | PascalCase | `UserController.php` |
| Laravel services | PascalCase | `AuditLogService.php` |
| Vue components | PascalCase | `ConfirmModal.vue` |
| JSON data files | kebab-case | `provinces.json`, `status-options.json` |
| Markdown docs | kebab-case | `deployment-checklist.md` |
| Uploaded files | uuid or timestamp prefix | `1720000000_profile.jpg` |

---

## Do Not

- Never use single-letter variable names except loop counters (`$i`, `$j`)
- Never use abbreviations that aren't obvious (`$usrNm` → use `$userName`)
- Never name a variable `$data`, `$result`, `$temp` without context
- Never use spaces in file names