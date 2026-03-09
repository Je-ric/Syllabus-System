<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CollegeDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'College of Agriculture' => [
                'Department of Agricultural Extension Education',
                'Department of Agri-Management',
            ],
            'College of Arts and Social Sciences' => [
                'Department of Communication and Development Studies',
                'Department of English and Humanities',
                'Department of Filipino',
                'Department of Global Studies',
                'Department of Social Science',
                'Department of Psychology',
            ],
            'College of Business and Accountancy' => [
                'Department of Accountancy',
                'Department of Business',
            ],
            'College of Education' => [
                'Department of Early Childhood Education',
                'Department of Language, Culture, and Arts Education',
                'Department of Technology, Livelihood, and Life Skills Education',
                'Department of Science Education',
            ],
            'College of Engineering' => [
                'Department of Agricultural and Biosystems Engineering',
                'Department of Information Technology',
                'Department of Civil Engineering',
            ],
            'College of Fisheries' => [
                'Department of Aquaculture',
                'Department of Aquatic Resources, Ecology, and Management',
            ],
            'College of Home Science and Insdustry' => [
                'Department of Food Science and Technology',
                'Department of Hospitality and Tourism',
                'Department of Textile and Garment Technology',
            ],
            'College of Veterinary Science and Medicine' => [],
            'College of Science' => [
                'Department of Biological Sciences',
                'Department of Chemistry',
                'Department of Environmental Science and Meteorology',
                'Department of Mathemathics and Physics',
                'Department of Statistics',
            ],
        ];

        foreach ($data as $collegeName => $departments) {
            $college = College::firstOrCreate(['name' => $collegeName]);

            foreach ($departments as $departmentName) {
                Department::firstOrCreate([
                    'college_id' => $college->id,
                    'name' => $departmentName,
                ]);
            }
        }
    }
}
