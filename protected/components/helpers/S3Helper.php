<?php

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;

class S3Helper
{
    private static $_client;

    public static function getClient()
    {
        if (!isset(self::$_client)) {
            // Validate required environment variables
            $requiredVars = ['AWS_REGION', 'AWS_ACCESS_KEY', 'AWS_SECRET'];
            $missingVars = [];
            
            foreach ($requiredVars as $var) {
                if (!isset($_ENV[$var]) || empty($_ENV[$var])) {
                    $missingVars[] = $var;
                }
            }
            
            if (!empty($missingVars)) {
                throw new InvalidArgumentException(
                    'Missing required client configuration options: ' . implode(', ', $missingVars)
                );
            }
            
            self::$_client = new S3Client([
                'region'       => $_ENV['AWS_REGION'],
                'version'     => '2006-03-01',
                'credentials' => [
                    'key' => $_ENV['AWS_ACCESS_KEY'],
                    'secret' => $_ENV['AWS_SECRET']
                ]
            ]);
        }
        return self::$_client;
    }
    public static function generatePUTObjectPreSignedUrl(string $key, string $bucketName, array $options)
    {
        $client = self::getClient();
        $request = $client->createPresignedRequest(
            $client->getCommand('PutObject', [
                'Bucket' => $bucketName,
                'Key' => $key,
            ]),
            $options['expiry'] ?? '+1 hour'
        );

        $presignedUrl = (string) $request->getUri();
        return $presignedUrl;
    }
    public static function generateGETObjectPreSignedUrl(string $key, string $bucketName, array $options = [])
    {
        $client = self::getClient();
        $request = $client->createPresignedRequest(
            $client->getCommand('GetObject', [
                'Bucket' => $bucketName,
                'Key' => $key,
            ]),
            $options['expiry'] ?? '+24 hour'
        );

        $presignedUrl = (string) $request->getUri();
        return $presignedUrl;
    }
    public static function uploadObject(string $key, string $fileContent, string $bucketName)
    {
        try {
            $client = self::getClient();
            $result = $client->putObject([
                'Bucket' => $bucketName,
                'Key' => $key,
                'Body' => $fileContent,
            ]);
            return ['success' => true, 'data' => $result];
        } catch (Exception $e) {
            Yii::log('S3 upload error: ' . $e->getMessage(), 'error', 'S3Helper');
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public static function deleteObject(string $key, string $bucketName)
    {
        $client = self::getClient();
        $result = $client->deleteObject([
            'Bucket' => $bucketName,
            'Key' => $key,
        ]);
        return $result;
    }
    public static function generateGETObjectUrl(string $key)
    {
        $bucketName = $_ENV['S3_BUCKET_NAME'];
        $region = $_ENV['AWS_REGION'];
        return sprintf('https://%s.s3.%s.amazonaws.com/%s', $bucketName, $region, $key);
    }
    
}