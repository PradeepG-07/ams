<?php

class AttendanceCommand extends CConsoleCommand
{
    public function run($args)
    {

        $today = date('Y-m-d');
        
        $students = Student::model()->findAll();
        

        foreach ($students as $student) {
            // Ensure attendance for today is initialized
            if (!isset($student->attendance[$today])) {
                $student->attendance[$today] = array();
            }

            $records = $student->attendance[$today];

            // Exclude non-numeric keys like 'todayspercentage'
            $classRecords = array_filter($records, function ($v, $k) {
                return is_numeric($k);
            }, ARRAY_FILTER_USE_BOTH);

            $total = count($classRecords);
            $present = 0;

            foreach ($classRecords as $entry) {
                if (isset($entry['status']) && $entry['status'] === 'present') {
                    $present++;
                }
            }

            $percentage = ($total > 0) ? round(($present / $total) * 100) : 0;

            // Add 'todayspercentage' as an associative key
            $student->attendance[$today]['todayspercentage'] = $percentage;

            $student->save();
        }

        echo "Updated todayspercentage for all students on $today\n";
    }
}
