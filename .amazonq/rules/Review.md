Review and refactor the provided code with a focus on maintainability, readability, consistency, and clean architecture.

### Code Quality & Validation

* Review all conditions, validations, and business rules.
* Identify and fix missing conditions, conflicting logic, unreachable code, and potential edge cases.
* Ensure error handling is appropriate and consistent, using try-catch blocks where necessary.

### Clean Code & Readability

* Apply DRY (Don't Repeat Yourself) principles.
* Eliminate duplicate logic, queries, and code blocks.
* Use clear, descriptive, and consistent naming for functions, variables, methods, and classes.
* Improve code readability by simplifying complex logic and reducing cognitive load.

### Refactoring

* Avoid deep nesting by using guard clauses, early returns, and inverted conditions where appropriate.
* Break down large methods into smaller, single-responsibility functions.
* Refactor lengthy controllers, services, and components into manageable and reusable units.
* Extract reusable logic into dedicated helper methods, services, traits, or model methods when appropriate.

### Database & Query Optimization

* Identify reusable queries, scopes, or data retrieval logic and move them to the model layer when applicable.
* Remove duplicated query logic across controllers and services.
* Ensure queries are efficient, maintainable, and reusable.

### UI/UX & User Safety

* Add confirmation dialogs/modals for destructive actions such as delete, remove, detach, or unassign operations.
* Review user flows for accidental actions and provide proper safeguards.

### Expected Outcome

The final code should be:

* Easier to read and maintain
* Modular and reusable
* Less repetitive
* Less nested
* Consistent across the codebase
* Aligned with clean architecture and Laravel best practices
* Safe for users through proper validation, confirmation, and error handling

NOTE: Too much is Bad. Make it easier to understand and READ
