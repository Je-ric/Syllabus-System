<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollegeDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Structure: College => Department => [Programs with roles]
        // Programs can be listed under multiple departments with 'primary' or 'supporting' roles
        $data = [
            'College of Agriculture' => [
                'Department of Agricultural Extension' => [
                    ['name' => 'BS Agriculture', 'role' => 'primary'],
                ],
                'Department of Animal Science' => [
                    ['name' => 'BS Agriculture', 'role' => 'supporting'],
                ],
                'Department of Agri-Management' => [
                    ['name' => 'BS Agriculture', 'role' => 'supporting'],
                    ['name' => 'BS Agribusiness', 'role' => 'primary'],
                ],
                'Department of Crop Science' => [
                    ['name' => 'BS Agriculture', 'role' => 'supporting'],
                ],
                'Department of Crop Protection' => [
                    ['name' => 'BS Agriculture', 'role' => 'supporting'],
                ],
                'Department of Soil Science' => [
                    ['name' => 'BS Agriculture', 'role' => 'supporting'],
                ],
            ],
            'College of Arts and Social Sciences' => [
                'Department of Communication & Development Studies' => [
                    ['name' => 'BS Development Communication', 'role' => 'primary'],
                ],
                'Department of English and Humanities' => [
                    ['name' => 'BA Literature', 'role' => 'primary'],
                ],
                'Department of Filipino' => [
                    ['name' => 'BA Filipino', 'role' => 'primary'],
                ],
                'Department of Global Studies' => [
                    ['name' => 'BA International Studies - Global Sustainable Development', 'role' => 'primary'],
                ],
                'Department of Social Sciences' => [
                    ['name' => 'BA Social Science', 'role' => 'primary'],
                ],
                'Department of Psychology' => [
                    ['name' => 'BS Psychology', 'role' => 'primary'],
                ],
            ],
            'College of Business and Accountancy' => [
                'Department of Accountancy' => [
                    ['name' => 'BS Accountancy', 'role' => 'primary'],
                    ['name' => 'BS Management Accounting', 'role' => 'primary'],
                ],
                'Department of Business' => [
                    ['name' => 'BS Business Administration', 'role' => 'primary'],
                    ['name' => 'BS Entrepreneurship', 'role' => 'primary'],
                ],
            ],
            'College of Education' => [
                'Department of Language, Culture & Arts Education' => [
                    ['name' => 'Bachelor of Culture and Arts Education', 'role' => 'primary'],
                    ['name' => 'Bachelor of Secondary Education', 'role' => 'supporting'],
                ],
                'Department of Early Childhood and Elementary Education' => [
                    ['name' => 'Bachelor of Early Childhood Education', 'role' => 'primary'],
                    ['name' => 'Bachelor of Elementary Education', 'role' => 'primary'],
                ],
                'Department of Sports and Physical Education' => [
                    ['name' => 'Bachelor of Physical Education', 'role' => 'primary'],
                ],
                'Department of Education Policy and Practice' => [
                    ['name' => 'Bachelor of Secondary Education', 'role' => 'primary'],
                ],
                'Department of Science Education' => [
                    ['name' => 'Bachelor of Secondary Education', 'role' => 'supporting'],
                ],
                'Department of Technology, Livelihood & Life Skills Education' => [
                    ['name' => 'Bachelor of Technology and Livelihood Education', 'role' => 'primary'],
                ],
            ],
            'College of Engineering' => [
                'Department of Agricultural and Biosystems Engineering' => [
                    ['name' => 'BS Agricultural and Biosystems Engineering', 'role' => 'primary'],
                ],
                'Department of Information Technology' => [
                    ['name' => 'BS Information Technology', 'role' => 'primary'],
                ],
                'Department of Civil Engineering' => [
                    ['name' => 'BS Civil Engineering', 'role' => 'primary'],
                ],
            ],
            'College of Fisheries' => [
                'Department of Aquaculture' => [
                    ['name' => 'Bachelor of Science in Fisheries', 'role' => 'primary'],
                ],
                'Department of Aquatic Resources, Ecology & Management' => [
                    ['name' => 'Bachelor of Science in Fisheries', 'role' => 'supporting'],
                ],
                'Department of Aquatic Post-Harvest' => [
                    ['name' => 'Bachelor of Science in Fisheries', 'role' => 'supporting'],
                ],
            ],
            'College of Home Science and Industry' => [
                'Department of Food Science and Technology' => [
                    ['name' => 'BS Food Technology', 'role' => 'primary'],
                ],
                'Department of Hospitality & Tourism Management' => [
                    ['name' => 'BS Hospitality Management', 'role' => 'primary'],
                    ['name' => 'BS Tourism Management', 'role' => 'primary'],
                ],
                'Department of Textile and Garment Technology' => [
                    ['name' => 'BS Fashion and Textile Technology', 'role' => 'primary'],
                ],
            ],
            'College of Science' => [
                'Department of Biological Sciences' => [
                    ['name' => 'BS Biology', 'role' => 'primary'],
                ],
                'Department of Chemistry' => [
                    ['name' => 'BS Chemistry', 'role' => 'primary'],
                ],
                'Department of Environmental Science and Meteorology' => [
                    ['name' => 'BS Environmental Science', 'role' => 'primary'],
                    ['name' => 'BS Meteorology', 'role' => 'primary'],
                ],
                'Department of Mathematics and Physics' => [
                    ['name' => 'BS Mathematics', 'role' => 'primary'],
                ],
                'Department of Statistics' => [
                    ['name' => 'BS Statistics', 'role' => 'primary'],
                ],
            ],
            'College of Veterinary Science and Medicine' => [
                'Department of Basic Veterinary Sciences' => [
                    ['name' => 'Doctor of Veterinary Medicine', 'role' => 'primary'],
                ],
                'Department of Veterinary Paraclinical Sciences' => [
                    ['name' => 'Doctor of Veterinary Medicine', 'role' => 'supporting'],
                ],
                'Department of Veterinary Clinical Sciences' => [
                    ['name' => 'Doctor of Veterinary Medicine', 'role' => 'supporting'],
                ],
            ],
        ];

        // Track programs by name to handle many-to-many relationships
        $programRegistry = [];

        DB::transaction(function () use ($data, &$programRegistry) {
            foreach ($data as $collegeName => $departments) {
                $college = College::firstOrCreate(['name' => $collegeName]);

                foreach ($departments as $departmentName => $programs) {
                    $department = Department::firstOrCreate([
                        'college_id' => $college->id,
                        'name' => $departmentName,
                    ]);

                    foreach ($programs as $programData) {
                        $programName = $programData['name'];
                        $role = $programData['role'];

                        // Create program only if it doesn't exist yet
                        if (!isset($programRegistry[$programName])) {
                            $program = Program::firstOrCreate(['name' => $programName]);
                            $programRegistry[$programName] = $program;
                        } else {
                            $program = $programRegistry[$programName];
                        }

                        // Attach department to program with the specified role
                        // Using updateOrCreate to avoid duplicate pivot entries
                        DB::table('program_departments')->updateOrInsert(
                            [
                                'program_id' => $program->id,
                                'department_id' => $department->id,
                            ],
                            [
                                'role' => $role,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                }
            }
        });
    }
}
