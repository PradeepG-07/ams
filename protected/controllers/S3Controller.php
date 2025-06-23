<?php
 
class S3Controller extends CController
{
    public function actionUpload()
    {
 
        
        if (isset($_FILES['userfile'])) {
            $file = $_FILES['userfile'];
            $sourcePath = $file['tmp_name'];
            $originalName = $file['name'];
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
 
            // Generate a unique key (e.g., using user ID and timestamp)
            $s3Key = 'uploads/' . time() . '_' . uniqid() . '.' . $extension;
 
            //Upload as public (directly accessible via S3/CloudFront URL)
            $s3Response = S3Helper::uploadFile($sourcePath, $s3Key, 'public-read');
 
            // Upload as private (accessible via presigned URLs or CloudFront OAI)
            //$objectUrl = S3Helper::uploadFile($sourcePath, $s3Key);
 
            $objectUrl= $s3Response['ObjectURL'] ?? false;
 
            if ($objectUrl !== false) {
                echo "Upload successful! Object Key: " . $s3Key;
            
                $cloudFrontUrl = S3Helper::getAssetUrl($s3Key);
                echo "<br>CloudFront URL: " . $cloudFrontUrl;
            
                // Later, when deleting the record associated with this file:
                // S3Helper::deleteObject($s3Key);
 
            } else {
                echo "Upload failed.";
            }
        }
        $this->render('upload');
    }
    public function actionViewAll()
    {
        $client = S3Helper::getClient();
        $bucket = $_ENV['S3_BUCKET_NAME'];

        try {
            // List all objects under the "uploads/" folder
            $objects = $client->listObjectsV2([
                'Bucket' => $bucket,
                'Prefix' => 'uploads/', // Only files under uploads/
            ]);

            $files = [];
            if (isset($objects['Contents'])) {
                foreach ($objects['Contents'] as $object) {
                    $files[] = [
                        'key' => $object['Key'],
                        'url' => S3Helper::getAssetUrl($object['Key']),
                    ];
                }
            }

            $this->render('viewall', ['files' => $files]);

        } catch (AwsException $e) {
            echo "Error listing S3 objects: " . $e->getMessage();
            Yii::log("S3 List Error: " . $e->getMessage(), CLogger::LEVEL_ERROR, 'S3Helper');
        }
    }
 
    
 
}
 