<?php

/**
 * JWT Helper component for handling JWT token operations
 */
class JWTHelper extends CComponent
{
    /**
     * Generate a JWT token for a user
     * 
     * @param mixed $user_id The user ID
     * @param string $username The username
     * @param string $email The user email
     * @param string $userType The user type (admin, teacher, student)
     * @param int $expire Token expiration time in seconds (default 3600 = 1 hour)
     * @return string The JWT token
     */
    public static function generateToken($user_id, $email, $userType, $expire = 3600)
    {
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $expire = $issuedAt + $expire;
        
        // Token payload
        $payload = [
            'iat' => $issuedAt,     // Issued at time
            'jti' => $tokenId,      // JWT ID
            'iss' => Yii::app()->request->hostInfo, // Issuer
            'nbf' => $issuedAt,     // Not before
            'exp' => $expire,       // Expiration time
            'data' => [
                'user_id' => $user_id,
                'email' => $email,
                'user_type' => $userType
            ]
        ];
        
        // Get the secret key from configuration or use a default (you should change this)
        $secretKey = Yii::app()->params['jwtSecretKey'] ?? 'your-256-bit-secret';
        
        // Generate the JWT token
        return \Firebase\JWT\JWT::encode($payload, $secretKey, 'HS256');
    }
    
    /**
     * Validate and decode a JWT token
     * 
     * @param string $token The JWT token to validate
     * @return object|false The decoded token payload or false if invalid
     */
    public static function validateToken($token)
    {
        try {
            $secretKey = Yii::app()->params['jwtSecretKey'] ?? 'your-256-bit-secret';
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            // Log error
            Yii::log('JWT validation error: ' . $e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
        }
    }
}