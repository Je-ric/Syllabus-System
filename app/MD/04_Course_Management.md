# Course Management Rules

This document summarizes conditions for course creation, update, and PO mapping.

## Source Controller

- `app/Http/Controllers/CourseController.php`

## Listing Conditions

- Courses are filtered by `program_id` query when provided.
- If no `program_id`, listing renders with empty grouped courses.

## Create Form Conditions

- Program is loaded when `program_id` is provided.
- Program outcomes are loaded in `po_code` order for mapping UI.

## Course Create Conditions

- `program_id` is required and must exist.
- `code` is required and unique against `courses.course_code`.
- `name` is required.
- `description` is optional string.
- `credits` is required integer, minimum 1.
- `has_lec_lab` is optional boolean.
- `year_level` is optional integer between 1 and 5.
- `semester` is optional integer in `1,2`.
- `prerequisite` is optional string.
- `corequisite` is optional string.
- `po_mapping` is optional array.
- Each `po_mapping` value, when present, must be one of `I,E,D`.

## Course Create Behavior

- Stores course with normalized fields:
- `course_code`, `course_title`, `course_description`, `credit_units`, etc.
- Sets `created_by` as current authenticated user id.
- Calls model helper `syncPoMappings()` when PO mapping exists.

## Course Edit/Update Conditions

- Course is loaded with `program` and `programOutcomes`.
- Update validation is same as create, except `code` unique ignores current course id.

## Course Update Behavior

- Course fields are updated.
- PO mappings are rebuilt using `sync` with only valid IED entries.

## PO Mapping Rules

- Only `I`, `E`, and `D` are persisted.
- Empty/invalid mapping values are ignored.
- Existing mapping not present in submitted data is removed by `sync`.

