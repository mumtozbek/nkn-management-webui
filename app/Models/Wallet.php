<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'node_id',
        'address',
        'keystore',
        'password',
        'generated_at',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'node_id' => 'nullable|exists:nodes,id|unique:wallets,node_id,' . $this->id,
            'address' => 'required|string|min:36|max:36|unique:wallets,address,' . $this->id,
            'keystore' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Node relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    /**
     * Generate a new wallet.
     *
     * @param $password
     */
    public static function generate()
    {
        if (empty(env('NKN_BIN_DIR'))) {
            throw new \Exception('Invalid NKN_BIN_DIR env var.');
        }

        $nknc = env('NKN_BIN_DIR') . 'nknc';
        if (!is_file($nknc)) {
            throw new \Exception('NKNC not found.');
        }

        if (empty(env('NKN_DEFAULT_PASS'))) {
            throw new \Exception('Invalid NKN_DEFAULT_PASS env var.');
        }

        $walletFile = storage_path('nkn/wallet.json');

        if (is_file($walletFile)) {
            unlink($walletFile);
        }

        [$address, $privateKey] = explode('   ', exec(env('NKN_BIN_DIR') . 'nknc wallet -c -p ' . env('NKN_DEFAULT_PASS') . ' -n ' . $walletFile));

        $wallet = self::create([
            'address' => $address,
            'keystore' => file_get_contents($walletFile),
            'password' => env('NKN_DEFAULT_PASS'),
        ]);

        if (is_file($walletFile)) {
            unlink($walletFile);
        }

        return $wallet;
    }
}
