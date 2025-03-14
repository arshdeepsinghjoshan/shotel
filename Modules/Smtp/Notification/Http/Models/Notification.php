<?php

namespace Modules\Notification\Http\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Http\Models\Setting;

class Notification extends Model
{


    const STATE_INACTIVE = 0;

    const STATE_ACTIVE = 1;

    const STATE_DELETED = 2;

    const IS_NOT_READ = 0;

    const IS_READ = 1;

    /**
     *
     * @var string the api_url for Firebase cloude messageing.
     */
    public $apiUrl = 'https://fcm.googleapis.com/fcm/send';

    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'model_id',
        'model_type',
        'to_user_id',
        'is_read',
        'state_id'
    ];

    public function scopeSearchState($query, $search)
    {
        $stateOptions = self::getStateOptions();
        return $query->where(function ($query) use ($search, $stateOptions) {
            foreach ($stateOptions as $stateId => $stateName) {
                if (stripos($stateName, $search) !== false) {
                    $query->orWhere('state_id', $stateId);
                }
            }
        });
    }


    public function scopeIsRead($query, $search)
    {
        $isReadOptions = self::getIsReadOptions();
        return $query->where(function ($query) use ($search, $isReadOptions) {
            foreach ($isReadOptions as $isReadId => $isReadName) {
                if (stripos($isReadName, $search) !== false) {
                    $query->orWhere('is_read', $isReadId);
                }
            }
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }



    public static function getStateOptions()
    {
        return [
            self::STATE_INACTIVE => "New",
            self::STATE_ACTIVE => "Active",
            self::STATE_DELETED => "Archived"
        ];
    }

    public function getState()
    {
        $list = self::getStateOptions();
        return isset($list[$this->state_id]) ? $list[$this->state_id] : 'Not Defined';
    }

    public static function getIsReadOptions()
    {
        return [
            self::IS_NOT_READ => "Not Read",
            self::IS_READ => "Read"
        ];
    }

    public function getIsRead()
    {
        $list = self::getIsReadOptions();
        return isset($list[$this->is_read]) ? $list[$this->is_read] : 'Not Defined';
    }



    public function sendNotificationOnApp()
    {


        $androidtoken = [];
        $iostoken = [];
        $tokens = "";
        $data = [];
        $data['controller'] = app('request')->route()->getAction()['controller'];
        $data['action'] = app('request')->route()->getAction()['as'];
        $data['message'] = $this->title;
        $data['user_id'] = $this->to_user_id;
        $data['detail'] = $this;
        $user = User::find($this->to_user_id);

        if (!empty($user)) {
            $tokens = $user->getAuthSessions;
            if (count($tokens) > 0) {
                foreach ($tokens as $token) {
                    if ($token->device_type == 1) {
                        $androidtoken[] = $token->device_token;
                    }
                    if ($token->device_type == 2)
                        $iostoken[] = $token->device_token;
                }
                if (!empty($androidtoken)) {
                    try {
                        $datas = $this->sendDataMessage($androidtoken, $data);
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }


    public function sendDataMessage($tokens = [], $data = null)
    {
        $body = [
            'registration_ids' => $tokens,
            'notification' => [
                "body" => $data['detail']['title'],
                "title" => 'Ask Laravel admin',
                "action" => $data['action']
            ],

            'data' => $data
        ];
        return $this->send($body);
    }



    public function send($body)
    {

        $firebaseKey = Setting::where('key', 'firebase_key')->first();
        $this->authKey = !empty($firebaseKey) ? $firebaseKey->value : '';
        if (empty($this->authKey)) {
            return true;
        }

        $headers = [
            "Authorization:key={$this->authKey}",
            'Content-Type: application/json',
            'Expect: '
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FRESH_CONNECT => false,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_POSTFIELDS => json_encode($body)
        ]);

        $result = curl_exec($ch);
        $this->response = $result;

        if ($result === false) {
            throw new \Exception("Could not send notification");
        }
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code == 300) {
            throw new \Exception("Could not send notification");
        }
        curl_close($ch);
        $result = json_decode($result, true);
        return $result;
    }
}
