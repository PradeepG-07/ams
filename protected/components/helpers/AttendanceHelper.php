<?php
use MongoDB\BSON\ObjectId;
class AttendanceHelper{

    public static function saveAttendance($data){
        try{
            Yii::log("Saving attendance data", CLogger::LEVEL_INFO, 'application.helpers.AttendanceHelper');
            // check if date is <= today
            if(!isset($data['date']) || !strtotime($data['date']) || date('Y-m-d', strtotime($data['date'])) > date('Y-m-d')){
                Yii::log("Invalid date provided: " . $data['date'], CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'Invalid date provided. Date must be today or earlier.'
                );
            }

            // Validate class_id - handle empty class_id gracefully
            if (!isset($data['class_id']) || empty($data['class_id'])) {
                Yii::log("Empty class ID provided, cannot save attendance", CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'Class ID is required to save attendance.'
                );
            }
            
            if (!is_string($data['class_id']) || ClassesHelper::loadClassById(new ObjectId($data['class_id'])) === null) {
                Yii::log("Invalid class ID provided: " . $data['class_id'], CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'Invalid class ID provided.'
                );
            }
            if(!isset($data['student_ids'])){
                $model = new Attendance();
                $model->attributes = $data;
                $model->date = new MongoDate(strtotime($data['date']));
                $model->class_id = new ObjectId($data['class_id']);
                $model->student_ids = array();
                if(!$model->save()){
                    Yii::log("Failed to save attendance with no students: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.AttendanceHelper');
                    return array(
                        'success' => false,
                        'message' => 'Failed to save attendance with no students: ' . json_encode($model->getErrors())
                    );
                }
                Yii::log("Attendance saved successfully with no students for class ID: " . $data['class_id'], CLogger::LEVEL_INFO, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => true,
                    'message' => 'Attendance saved successfully with no students!'
                );
            }

            // Validate student_ids
            if (!is_array($data['student_ids'])) {
                Yii::log("invalid format", CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'invalid format.'
                );
            }
            if(StudentHelper::validateStudentIds($data['student_ids']) === false){
                Yii::log("Invalid student IDs provided: " . json_encode($data['student_ids']), CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'Invalid student IDs provided.'
                );
            }
            $existingModel = Attendance::model()->findByAttributes(array(
                'class_id' => new ObjectId($data['class_id']),
                'date' => date('Y-m-d', strtotime($data['date']))
            ));

            if($existingModel){
                $existingModel->delete();
                Yii::log("Existing attendance record found and deleted for class ID: " . $data['class_id'], CLogger::LEVEL_INFO, 'application.helpers.AttendanceHelper');
            }

            $model = new Attendance();
            $model->attributes = $data;
            $model->date = new MongoDate(strtotime($data['date']));
            $model->class_id = new ObjectId($data['class_id']);
            $model->student_ids = array_map(function($id) {
                return new ObjectId($id);
            }, $data['student_ids']);
            if(!$model->save()){
                Yii::log("Failed to save attendance: " . json_encode($model->getErrors()), CLogger::LEVEL_WARNING, 'application.helpers.AttendanceHelper');
                return array(
                    'success' => false,
                    'message' => 'Failed to save attendance: ' . json_encode($model->getErrors())
                );
            }
            Yii::log("Attendance saved successfully for class ID: " . $data['class_id'], CLogger::LEVEL_INFO, 'application.helpers.AttendanceHelper');
            return array(
                'success' => true,
                'message' => 'Attendance saved successfully!'
            );
        }
        catch(Exception $e){
            Yii::log("Error validating attendance data: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'application.helpers.AttendanceHelper');
            return array(
                'success' => false,
                'message' => 'An error occurred while validating attendance data: ' . $e->getMessage()
            );
        }
    }
}