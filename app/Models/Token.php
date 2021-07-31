<?php

namespace App\Models;

use App\Exceptions\CustomException;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Token extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'WebTokens';
    private static $expireTimeInHours = 1;

    use HasFactory;

    /**
     * Crea un token para un user Id dado
     * El user id puede ser cualquier identificador para el token de no más de 50 caracteres
     */
    public static function CreateToken($userId) {
        if (!$userId) {
            return null;
        }
        if(strlen($userId) > 50) {
            throw new CustomException('Identificador de usuario demasiado largo', 500);
        }
        $tokenModel = new Token();
        $tokenModel->token = crypt(time(), 'time$alt') . '-' . bin2hex(openssl_random_pseudo_bytes(8));
        $tokenModel->userId = $userId;
        $expirationDate = new DateTime();
        $expirationDate->add(new DateInterval('PT'.self::$expireTimeInHours.'H'));
        $tokenModel->expiresAt = $expirationDate;
        $tokenModel->save();
        return $tokenModel->token;
    }

    /**
     * Valida credenciales de acceso para consultas web
     */
    public static function ValidateWebToken($token, $patente, $codigo) {
        $currentTime = new DateTime();
        $tokenModel = Token::where('token', '=', $token)
            ->where('userId', '=', $patente.$codigo)
            ->where('expiresAt', '>', $currentTime->format("Y-m-d\TH:i:s"))
            ->get()
            ->first();
        if ($tokenModel) {
            $currentTime->add(new DateInterval('PT'.self::$expireTimeInHours.'H'));
            $tokenModel->expiresAt = $currentTime;
            $tokenModel->save();
        }
        return $tokenModel;
    }

    /**
     * Limpia las entradas de tokens vencidos
     */
    public static function CleanOldTokens() {
        $currentTime = (new DateTime())->format("Y-m-d\TH:i:s");
        $deletedRows = Token::where('expiresAt', '<', $currentTime)->delete();
        Log::info("Se eliminaron $deletedRows web tokens vencidos.");
    }
}
